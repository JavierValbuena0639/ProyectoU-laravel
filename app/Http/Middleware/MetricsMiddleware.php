<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MetricsMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $start = microtime(true);
        $response = $next($request);

        try {
            $durationMs = (int) round((microtime(true) - $start) * 1000);
            $userId = optional($request->user())->id;
            $ip = $request->ip();
            $method = $request->getMethod();
            $path = $request->path();
            $status = method_exists($response, 'getStatusCode') ? $response->getStatusCode() : null;
            $memoryMb = round(memory_get_usage(true) / (1024 * 1024), 2);

            // Estructura simple JSON para fácil ingestión
            $payload = [
                'ts' => now()->toISOString(),
                'method' => $method,
                'path' => $path,
                'status' => $status,
                'duration_ms' => $durationMs,
                'memory_mb' => $memoryMb,
                'ip' => $ip,
                'user_id' => $userId,
            ];
            Log::channel('metrics')->info('request', $payload);
        } catch (\Throwable $e) {
            // Evitar romper el flujo si logging falla
        }

        return $response;
    }
}