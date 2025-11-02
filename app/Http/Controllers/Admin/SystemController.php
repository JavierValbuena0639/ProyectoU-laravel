<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

class SystemController extends Controller
{
    public function verify()
    {
        $checks = [];
        $checks['app_version'] = app()->version();
        $checks['php_version'] = PHP_VERSION;
        $checks['environment'] = app()->environment();

        // Extensiones requeridas
        $requiredExtensions = ['zip', 'pdo', 'mbstring', 'openssl'];
        $extensions = [];
        foreach ($requiredExtensions as $ext) {
            $extensions[$ext] = extension_loaded($ext);
        }

        // Permisos de almacenamiento
        $storagePath = storage_path('app');
        $backupDir = storage_path('app/backups');
        if (!is_dir($backupDir)) {
            @mkdir($backupDir, 0775, true);
        }
        $checks['storage_writable'] = is_writable($storagePath);
        $checks['backup_dir_exists'] = is_dir($backupDir);
        $checks['backup_dir_writable'] = is_writable($backupDir);
        // Estado del primer arranque (marcador de respaldo inicial)
        $firstInitCompleted = File::exists($backupDir . DIRECTORY_SEPARATOR . '.boot_init_done');

        // Listar respaldos
        $backupFiles = [];
        foreach (glob($backupDir . DIRECTORY_SEPARATOR . '*') as $path) {
            if (is_file($path)) {
                $backupFiles[] = basename($path);
            }
        }

        // Auditoría: últimas líneas del log
        $auditEntries = [];
        $auditPath = storage_path('logs/audit.log');
        if (File::exists($auditPath)) {
            try {
                $content = File::get($auditPath);
                $lines = array_filter(explode("\n", trim($content)));
                $count = count($lines);
                $auditEntries = array_slice($lines, max($count - 20, 0));
            } catch (\Throwable $e) {
                $auditEntries = [];
            }
        }

        // Conexión a BD
        $dbOk = false;
        $dbError = null;
        try {
            DB::connection()->getPdo();
            $dbOk = true;
        } catch (\Throwable $e) {
            $dbOk = false;
            $dbError = $e->getMessage();
        }

        // Cache operativa
        $cacheOk = false;
        try {
            Cache::put('healthcheck', 'ok', now()->addMinutes(2));
            $cacheOk = Cache::get('healthcheck') === 'ok';
        } catch (\Throwable $e) {
            $cacheOk = false;
        }

        // Configuración relevante
        $config = [
            'DB_CONNECTION' => env('DB_CONNECTION'),
            'DB_HOST' => env('DB_HOST'),
            'DB_PORT' => env('DB_PORT'),
            'DB_DATABASE' => env('DB_DATABASE'),
            'DB_USERNAME' => env('DB_USERNAME'),
            'MAIL_MAILER' => env('MAIL_MAILER'),
            'DB_AUTO_BACKUP' => env('DB_AUTO_BACKUP', false),
        ];

        return view('admin.system-verify', compact(
            'checks', 'extensions', 'backupFiles', 'dbOk', 'dbError', 'cacheOk', 'config', 'auditEntries', 'firstInitCompleted'
        ));
    }
}