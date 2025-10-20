<?php

namespace App\Support;

use App\Models\Invoice;
use App\Models\FeResolution;
use Illuminate\Support\Facades\Log;

class FeDianService
{
    protected string $softwareId;
    protected string $softwarePin;
    protected string $certPath;
    protected string $certPassword;
    protected string $environment; // 'test' or 'prod'

    public function __construct(array $config = [])
    {
        $this->softwareId = (string) ($config['software_id'] ?? config('fe.software_id'));
        $this->softwarePin = (string) ($config['software_pin'] ?? config('fe.software_pin'));
        $this->certPath = (string) ($config['cert_path'] ?? config('fe.cert_path'));
        $this->certPassword = (string) ($config['cert_password'] ?? config('fe.cert_password'));
        $this->environment = (string) ($config['environment'] ?? config('fe.environment'));
    }

    /**
     * Stub de envío de factura a DIAN. 
     * Retorna estructura simple de resultado.
     */
    public function send(Invoice $invoice): array
    {
        // Obtener resolución activa (si existe)
        $resolution = FeResolution::where('active', true)->orderByDesc('id')->first();

        // Simular construcción y firma de UBL 2.1 (pendiente implementación real)
        $payload = [
            'invoice_id' => $invoice->id,
            'invoice_number' => $invoice->invoice_number,
            'date' => optional($invoice->invoice_date)->format('Y-m-d'),
            'total' => $invoice->total_amount,
            'currency' => $invoice->currency,
            'supplier_id' => $invoice->supplier_id,
            'user_id' => $invoice->user_id,
            'resolution' => $resolution ? [
                'prefix' => $resolution->prefix,
                'range' => [$resolution->number_from, $resolution->number_to],
                'validity' => [$resolution->start_date, $resolution->end_date],
            ] : null,
            'environment' => $this->environment,
        ];

        // Registro de auditoría del intento de envío
        try {
            Log::channel('audit')->info('DIAN send queued', [
                'software_id' => substr($this->softwareId, 0, 6) . '...',
                'invoice' => $payload,
            ]);
        } catch (\Throwable $e) {
            // No detener flujo por auditoría
        }

        // Respuesta simulada
        return [
            'status' => 'queued',
            'message' => 'Invoice queued for DIAN (stub)',
            'environment' => $this->environment,
        ];
    }
}