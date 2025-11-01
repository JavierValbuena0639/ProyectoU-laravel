<?php

namespace App\Support;

use App\Models\Invoice;
use App\Models\FeResolution;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
     * Garantiza que el servicio opere únicamente en entorno de pruebas.
     */
    protected function ensureSandbox(): void
    {
        if (strtolower($this->environment) !== 'test') {
            throw new \RuntimeException('DIAN service is restricted to test environment only.');
        }
    }

    /**
     * Stub de envío de factura a DIAN. 
     * Retorna estructura simple de resultado.
     */
    public function send(Invoice $invoice): array
    {
        // Bloqueo estricto: solo entorno de pruebas
        $this->ensureSandbox();

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

        // Persistir artefactos en sandbox (XML y JSON)
        $artifactPaths = $this->persistSandboxArtifacts($invoice, $payload);

        // Actualizar campos FE mínimos en la factura
        try {
            $invoice->update([
                'fe_status' => 'sent',
                'fe_xml_path' => $artifactPaths['xml_path'] ?? null,
                'fe_request_path' => $artifactPaths['request_path'] ?? null,
                'fe_response_path' => $artifactPaths['response_path'] ?? null,
            ]);
        } catch (\Throwable $e) {
            // No detener flujo por actualización de factura
        }

        // Registro de auditoría del intento de envío
        try {
            Log::channel('audit')->info('DIAN send queued', [
                'software_id' => substr($this->softwareId, 0, 6) . '...',
                'invoice' => $payload,
                'artifacts' => $artifactPaths,
            ]);
        } catch (\Throwable $e) {
            // No detener flujo por auditoría
        }

        // Respuesta simulada
        return [
            'status' => 'queued',
            'message' => 'Invoice queued for DIAN (stub)',
            'environment' => $this->environment,
            'artifacts' => $artifactPaths,
        ];
    }

    /**
     * Guardar artefactos de sandbox (XML/JSON) bajo storage/app/private/fe/YYYY/MM.
     */
    protected function persistSandboxArtifacts(Invoice $invoice, array $payload): array
    {
        try {
            $year = now()->format('Y');
            $month = now()->format('m');
            $baseDir = "private/fe/{$year}/{$month}";
            $prefix = 'INV-';
            $cleanNumber = preg_replace('/[^A-Za-z0-9_-]/', '', (string) $invoice->invoice_number);
            $basename = $prefix . $cleanNumber;

            // XML stub (no UBL real todavía)
            $xmlContent = $this->buildXmlStub($invoice, $payload);
            $xmlPath = "$baseDir/{$basename}.xml";
            Storage::put($xmlPath, $xmlContent);

            // JSON del request/payload
            $requestPath = "$baseDir/{$basename}-request.json";
            Storage::put($requestPath, json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            // JSON de respuesta simulada
            $response = [
                'status' => 'queued',
                'queued_at' => now()->toIso8601String(),
                'environment' => $this->environment,
                'uuid' => (string) Str::uuid(),
            ];
            $responsePath = "$baseDir/{$basename}-response.json";
            Storage::put($responsePath, json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            return [
                'xml_path' => Storage::path($xmlPath),
                'request_path' => Storage::path($requestPath),
                'response_path' => Storage::path($responsePath),
            ];
        } catch (\Throwable $e) {
            try {
                Log::error('Persist sandbox artifacts failed', ['error' => $e->getMessage()]);
            } catch (\Throwable $logErr) {}
            return [];
        }
    }

    /**
     * Construye un XML mínimo de stub para sandbox.
     */
    protected function buildXmlStub(Invoice $invoice, array $payload): string
    {
        $resolution = $payload['resolution'] ?? null;
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<InvoiceStub>'; 
        $xml .= '<ID>' . htmlspecialchars($invoice->invoice_number) . '</ID>';
        $xml .= '<IssueDate>' . htmlspecialchars(optional($invoice->invoice_date)->format('Y-m-d')) . '</IssueDate>';
        $xml .= '<DocumentCurrencyCode>' . htmlspecialchars($invoice->currency ?? 'COP') . '</DocumentCurrencyCode>';
        $xml .= '<LegalMonetaryTotal>' . number_format((float) $invoice->total_amount, 2, '.', '') . '</LegalMonetaryTotal>';
        if ($resolution) {
            $xml .= '<Resolution>'; 
            $xml .= '<Prefix>' . htmlspecialchars($resolution['prefix'] ?? '') . '</Prefix>';
            $xml .= '<RangeFrom>' . htmlspecialchars((string) ($resolution['range'][0] ?? '')) . '</RangeFrom>';
            $xml .= '<RangeTo>' . htmlspecialchars((string) ($resolution['range'][1] ?? '')) . '</RangeTo>';
            $xml .= '</Resolution>';
        }
        $xml .= '<Environment>' . htmlspecialchars($this->environment) . '</Environment>';
        $xml .= '</InvoiceStub>';
        return $xml;
    }

    /**
     * Prueba de habilitación (sandbox): realiza verificación mínima del entorno.
     */
    public function habilitationTest(): array
    {
        // Bloqueo estricto: solo entorno de pruebas
        $this->ensureSandbox();

        try {
            Log::channel('audit')->info('DIAN habilitation test executed', [
                'software_id' => substr($this->softwareId, 0, 6) . '...',
                'environment' => $this->environment,
            ]);
        } catch (\Throwable $e) {
            // Ignorar errores de auditoría
        }

        return [
            'status' => 'ok',
            'message' => 'Habilitation test completed in sandbox',
            'environment' => $this->environment,
        ];
    }
}