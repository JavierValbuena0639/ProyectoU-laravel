<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\FeResolution;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

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
}