<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\FeResolution;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\Invoice;
use App\Support\FeDianService;

class FeController extends Controller
{
    /**
     * Mostrar página de configuración de Facturación Electrónica DIAN
     */
    public function index()
    {
        $resolution = FeResolution::where('active', true)->orderByDesc('id')->first();
        $feConfig = [
            'software_id' => config('fe.software_id'),
            'software_pin' => config('fe.software_pin'),
            'cert_path' => config('fe.cert_path'),
            'cert_password' => config('fe.cert_password'),
            'environment' => config('fe.environment'),
        ];

        return view('admin.fe-config', compact('resolution', 'feConfig'));
    }

    /**
     * Guardar resolución de facturación (prefijo, rango y vigencia)
     */
    public function save(Request $request)
    {
        $validated = $request->validate([
            'prefix' => ['required','string','max:10'],
            'number_from' => ['required','integer','min:1'],
            'number_to' => ['required','integer','gt:number_from'],
            'start_date' => ['required','date'],
            'end_date' => ['required','date','after:start_date'],
        ]);

        DB::transaction(function () use ($validated) {
            // Desactivar resolución anterior activa
            FeResolution::where('active', true)->update(['active' => false]);
            // Crear nueva resolución activa
            FeResolution::create([
                'prefix' => $validated['prefix'],
                'number_from' => $validated['number_from'],
                'number_to' => $validated['number_to'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'active' => true,
            ]);
        });

        // Auditoría: registrar creación de nueva resolución activa
        try {
            Log::channel('audit')->info('FE resolution saved', [
                'user_id' => optional(Auth::user())->id,
                'prefix' => $validated['prefix'],
                'number_from' => $validated['number_from'],
                'number_to' => $validated['number_to'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
            ]);
        } catch (\Throwable $e) {
            // No bloquear flujo por errores de log
        }

        return redirect()->route('admin.fe.config')->with('success', __('admin.fe_config_saved'));
    }

    /**
     * Enviar una factura a DIAN (stub).
     */
    public function sendInvoice(Invoice $invoice, Request $request)
    {
        $service = new FeDianService([
            'software_id' => config('fe.software_id'),
            'software_pin' => config('fe.software_pin'),
            'cert_path' => config('fe.cert_path'),
            'cert_password' => config('fe.cert_password'),
            'environment' => config('fe.environment'),
        ]);

        try {
            // Validaciones de resolución (rango y fechas)
            $resolution = FeResolution::where('active', true)->orderByDesc('id')->first();
            if (!$resolution) {
                return redirect()->route('invoicing.invoices')
                    ->withErrors(['dian' => 'No hay una resolución DIAN activa configurada.']);
            }

            $today = now()->toDateString();
            if ($today < $resolution->start_date->toDateString() || $today > $resolution->end_date->toDateString()) {
                return redirect()->route('invoicing.invoices')
                    ->withErrors(['dian' => 'La resolución DIAN no está vigente para la fecha actual.']);
            }

            // Extraer número consecutivo de la factura (solo dígitos)
            $numberDigits = preg_replace('/\D/', '', (string) $invoice->invoice_number);
            $invoiceConsecutive = (int) ($numberDigits ?: 0);
            if ($invoiceConsecutive < $resolution->number_from || $invoiceConsecutive > $resolution->number_to) {
                return redirect()->route('invoicing.invoices')
                    ->withErrors(['dian' => 'El número de la factura está fuera del rango autorizado por la resolución.']);
            }

            // Alerta por límite cercano (últimos 10 números de la resolución)
            $remaining = (int) ($resolution->number_to - $invoiceConsecutive);
            if ($remaining >= 0 && $remaining <= 10) {
                session()->flash('warning', "Atención: sólo quedan {$remaining} números disponibles en esta resolución DIAN.");
            }

            $result = $service->send($invoice);

            // Auditoría básica
            try {
                Log::channel('audit')->info('DIAN send success', [
                    'user_id' => optional(Auth::user())->id,
                    'invoice_id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'result' => $result,
                ]);
            } catch (\Throwable $e) {
                // No interrumpir por errores de log
            }

            return redirect()->route('invoicing.invoices')->with('success', __('invoicing.sent_to_dian_success'));
        } catch (\Throwable $e) {
            try {
                Log::error('DIAN send failed', [
                    'error' => $e->getMessage(),
                    'invoice_id' => $invoice->id ?? null,
                ]);
            } catch (\Throwable $logErr) {}

            return redirect()->route('invoicing.invoices')->withErrors(['dian' => __('invoicing.sent_to_dian_error')]);
        }
    }

    /**
     * Ejecutar prueba de habilitación (sandbox) y volver a la configuración.
     */
    public function test(Request $request)
    {
        $service = new FeDianService([
            'software_id' => config('fe.software_id'),
            'software_pin' => config('fe.software_pin'),
            'cert_path' => config('fe.cert_path'),
            'cert_password' => config('fe.cert_password'),
            'environment' => config('fe.environment'),
        ]);

        try {
            $result = $service->habilitationTest();
            try {
                Log::channel('audit')->info('DIAN habilitation test success', [
                    'user_id' => optional(Auth::user())->id,
                    'result' => $result,
                ]);
            } catch (\Throwable $e) {}

            return redirect()->route('admin.fe.config')
                ->with('success', 'Prueba de habilitación ejecutada en entorno de pruebas.');
        } catch (\Throwable $e) {
            try {
                Log::error('DIAN habilitation test failed', [
                    'error' => $e->getMessage(),
                ]);
            } catch (\Throwable $logErr) {}
            return redirect()->route('admin.fe.config')
                ->withErrors(['dian' => 'Error al ejecutar la prueba de habilitación.']);
        }
    }
}