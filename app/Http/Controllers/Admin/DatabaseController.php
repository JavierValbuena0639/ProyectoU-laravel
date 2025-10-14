<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DatabaseController extends Controller
{
    public function index()
    {
        $config = [
            'DB_CONNECTION' => env('DB_CONNECTION'),
            'DB_HOST' => env('DB_HOST'),
            'DB_PORT' => env('DB_PORT'),
            'DB_DATABASE' => env('DB_DATABASE'),
            'DB_USERNAME' => env('DB_USERNAME'),
        ];
        $engine = strtoupper($config['DB_CONNECTION'] ?? 'mysql');
        return view('admin.database', compact('config', 'engine'));
    }

    public function testConnection(Request $request)
    {
        try {
            DB::connection()->getPdo();
            return back()->with('success', 'Conexión a la base de datos exitosa');
        } catch (\Throwable $e) {
            return back()->with('error', 'Error de conexión: ' . $e->getMessage());
        }
    }

    public function saveConnection(Request $request)
    {
        $validated = $request->validate([
            'DB_CONNECTION' => ['required', 'in:mysql,sqlite,pgsql'],
            'DB_HOST' => ['nullable', 'string'],
            'DB_PORT' => ['nullable', 'string'],
            'DB_DATABASE' => ['nullable', 'string'],
            'DB_USERNAME' => ['nullable', 'string'],
            'DB_PASSWORD' => ['nullable', 'string'],
        ]);

        $this->writeEnv($validated);

        return back()->with('success', 'Configuración guardada. Reinicie la app o limpie caché.');
    }

    private function writeEnv(array $data): void
    {
        $envPath = base_path('.env');
        $env = file_exists($envPath) ? file_get_contents($envPath) : '';

        foreach ($data as $key => $value) {
            $pattern = "/^" . preg_quote($key, '/') . "=.*/m";
            $replacement = $key . '=' . $this->escapeEnvValue($value);
            if (preg_match($pattern, $env)) {
                $env = preg_replace($pattern, $replacement, $env);
            } else {
                $env .= "\n" . $replacement;
            }
        }

        file_put_contents($envPath, $env);
    }

    private function escapeEnvValue($value): string
    {
        $value = (string)($value ?? '');
        if (str_contains($value, ' ') || str_contains($value, '#')) {
            return '"' . addslashes($value) . '"';
        }
        return $value;
    }
}