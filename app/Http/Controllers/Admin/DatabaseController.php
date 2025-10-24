<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use App\Models\User;
use App\Models\Account;
use App\Models\Invoice;
use App\Models\Transaction;
use App\Models\AdminUserAccount;

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
        // Auto backup flag
        $autoBackup = filter_var(env('DB_AUTO_BACKUP', false), FILTER_VALIDATE_BOOLEAN);

        // Listar respaldos existentes
        $backupDir = storage_path('app/backups');
        if (!is_dir($backupDir)) {
            @mkdir($backupDir, 0775, true);
        }
        $backups = [];
        foreach (glob($backupDir . DIRECTORY_SEPARATOR . '*') as $path) {
            if (is_file($path)) {
                $backups[] = [
                    'name' => basename($path),
                    'size' => filesize($path),
                    'mtime' => filemtime($path),
                ];
            }
        }
        usort($backups, function ($a, $b) { return $b['mtime'] <=> $a['mtime']; });

        // Métricas solicitadas
        $user = Auth::user();
        $isSupport = $user ? $user->isSupport() : false;
        $domain = optional($user)->emailDomain();

        // Conteos de usuarios (global si soporte, por dominio si no)
        $usersQuery = User::query();
        if (!$isSupport && $domain) {
            $usersQuery->where('email', 'like', '%@' . $domain);
        }
        $adminsCount = (clone $usersQuery)
            ->whereHas('role', function ($q) { $q->where('name', 'admin'); })
            ->count();
        $totalUsers = (clone $usersQuery)->count();

        // Tamaño total de la base de datos (siempre global)
        $dbSizeBytes = $this->getDatabaseSizeBytes();
        $dbSizeHuman = $this->formatBytes($dbSizeBytes);

        // Stats de tablas: global si soporte, por dominio si no
        $domainForStats = $isSupport ? null : $domain;
        $tableStats = $this->getTableStats($domainForStats);
        $fileUsage = $this->computeFileUsage();
        $fileUsageDomain = $this->computeFileUsageByDomain($domainForStats);

        return view('admin.database', compact(
            'config',
            'engine',
            'backups',
            'autoBackup',
            'adminsCount',
            'totalUsers',
            'dbSizeHuman',
            'tableStats',
            'fileUsage',
            'fileUsageDomain',
            'domain',
            'isSupport'
        ));
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

        try {
            $logValues = $validated;
            unset($logValues['DB_PASSWORD']);
            Log::channel('audit')->info('DB connection saved', [
                'user_id' => optional(Auth::user())->id,
                'values' => $logValues,
            ]);
        } catch (\Throwable $e) {
            // evitar bloquear flujo por errores de log
        }

        return back()->with('success', 'Configuración guardada. Reinicie la app o limpie caché.');
    }

    public function createBackup()
    {
        try {
            $backupDir = storage_path('app/backups');
            if (!is_dir($backupDir)) {
                @mkdir($backupDir, 0775, true);
            }

            $filename = 'backup_' . date('Y_m_d_His') . '.zip';
            $zipPath = $backupDir . DIRECTORY_SEPARATOR . $filename;

            $zip = new \ZipArchive();
            if ($zip->open($zipPath, \ZipArchive::CREATE) !== true) {
                return back()->with('error', 'No se pudo crear el archivo de respaldo.');
            }

            foreach ($this->getTableNames() as $table) {
                try {
                    $rows = DB::table($table)->get();
                    $zip->addFromString($table . '.json', json_encode($rows, JSON_PRETTY_PRINT));
                } catch (\Throwable $e) {
                    // Continuar con otras tablas
                }
            }

            $zip->close();
            try {
                Log::channel('audit')->info('Backup created', [
                    'user_id' => optional(Auth::user())->id,
                    'file' => $filename,
                ]);
            } catch (\Throwable $e) {}
            return back()->with('success', 'Respaldo creado: ' . $filename);
        } catch (\Throwable $e) {
            try {
                Log::channel('audit')->error('Backup create error', [
                    'user_id' => optional(Auth::user())->id,
                    'error' => $e->getMessage(),
                ]);
            } catch (\Throwable $e2) {}
            return back()->with('error', 'Error creando respaldo: ' . $e->getMessage());
        }
    }

    public function downloadBackup(string $file)
    {
        // Evitar path traversal
        if (!preg_match('/^[a-zA-Z0-9._-]+$/', $file)) {
            abort(404);
        }
        $path = 'backups/' . $file;
        if (!Storage::disk('local')->exists($path)) {
            abort(404);
        }
        try {
            Log::channel('audit')->info('Backup downloaded', [
                'user_id' => optional(Auth::user())->id,
                'file' => $file,
            ]);
        } catch (\Throwable $e) {}
        return Storage::disk('local')->download($path);
    }

    public function deleteBackup(string $file)
    {
        if (!preg_match('/^[a-zA-Z0-9._-]+$/', $file)) {
            return back()->with('error', 'Nombre de archivo inválido');
        }
        $path = 'backups/' . $file;
        $absPath = storage_path('app/' . $path);

        $deleted = false;
        try {
            if (Storage::disk('local')->exists($path)) {
                $deleted = Storage::disk('local')->delete($path);
            } elseif (File::exists($absPath)) {
                $deleted = File::delete($absPath);
            }
        } catch (\Throwable $e) {
            $deleted = false;
        }

        if ($deleted) {
            try {
                Log::channel('audit')->info('Backup deleted', [
                    'user_id' => optional(Auth::user())->id,
                    'file' => $file,
                ]);
            } catch (\Throwable $e) {}
            return back()->with('success', 'Respaldo eliminado: ' . $file);
        }

        // Mensaje más preciso según existencia
        $existsNow = Storage::disk('local')->exists($path) || File::exists($absPath);
        if (!$existsNow) {
            return back()->with('error', 'Respaldo no encontrado');
        }

        return back()->with('error', 'No se pudo eliminar el respaldo');
    }

    public function toggleAutoBackup(Request $request)
    {
        $enabled = $request->boolean('enabled');
        $this->writeEnv(['DB_AUTO_BACKUP' => $enabled ? 'true' : 'false']);
        try {
            Log::channel('audit')->info('Auto backup toggled', [
                'user_id' => optional(Auth::user())->id,
                'enabled' => $enabled,
            ]);
        } catch (\Throwable $e) {}
        return back()->with('success', 'Respaldo automático ' . ($enabled ? 'activado' : 'desactivado'));
    }

    public function runMigrations()
    {
        try {
            Artisan::call('migrate', ['--force' => true]);
            try {
                Log::channel('audit')->info('Migrations executed', [
                    'user_id' => optional(Auth::user())->id,
                ]);
            } catch (\Throwable $e) {}
            return back()->with('success', 'Migraciones ejecutadas correctamente');
        } catch (\Throwable $e) {
            try {
                Log::channel('audit')->error('Migrations error', [
                    'user_id' => optional(Auth::user())->id,
                    'error' => $e->getMessage(),
                ]);
            } catch (\Throwable $e2) {}
            return back()->with('error', 'Error al ejecutar migraciones: ' . $e->getMessage());
        }
    }

    public function rollbackMigrations()
    {
        try {
            Artisan::call('migrate:rollback', ['--force' => true]);
            try {
                Log::channel('audit')->info('Migrations rolled back', [
                    'user_id' => optional(Auth::user())->id,
                ]);
            } catch (\Throwable $e) {}
            return back()->with('success', 'Rollback de migraciones ejecutado');
        } catch (\Throwable $e) {
            try {
                Log::channel('audit')->error('Rollback error', [
                    'user_id' => optional(Auth::user())->id,
                    'error' => $e->getMessage(),
                ]);
            } catch (\Throwable $e2) {}
            return back()->with('error', 'Error al revertir migraciones: ' . $e->getMessage());
        }
    }

    public function optimizeDatabase()
    {
        try {
            Artisan::call('optimize');
            try {
                Log::channel('audit')->info('Optimize executed', [
                    'user_id' => optional(Auth::user())->id,
                ]);
            } catch (\Throwable $e) {}
            return back()->with('success', 'Optimización ejecutada');
        } catch (\Throwable $e) {
            try {
                Log::channel('audit')->error('Optimize error', [
                    'user_id' => optional(Auth::user())->id,
                    'error' => $e->getMessage(),
                ]);
            } catch (\Throwable $e2) {}
            return back()->with('error', 'Error al optimizar: ' . $e->getMessage());
        }
    }

    public function clearCache()
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');
            try {
                Log::channel('audit')->info('Caches cleared', [
                    'user_id' => optional(Auth::user())->id,
                ]);
            } catch (\Throwable $e) {}
            return back()->with('success', 'Caches limpiadas');
        } catch (\Throwable $e) {
            try {
                Log::channel('audit')->error('Cache clear error', [
                    'user_id' => optional(Auth::user())->id,
                    'error' => $e->getMessage(),
                ]);
            } catch (\Throwable $e2) {}
            return back()->with('error', 'Error al limpiar caches: ' . $e->getMessage());
        }
    }

    private function getTableNames(): array
    {
        $driver = config('database.default');
        $tables = [];
        try {
            if ($driver === 'mysql') {
                $dbName = DB::getDatabaseName();
                $rows = DB::select('SHOW TABLES');
                foreach ($rows as $row) {
                    $props = (array) $row;
                    $tables[] = reset($props);
                }
            } elseif ($driver === 'sqlite') {
                $rows = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
                foreach ($rows as $row) {
                    $tables[] = $row->name;
                }
            } elseif ($driver === 'pgsql') {
                $rows = DB::select("SELECT tablename FROM pg_catalog.pg_tables WHERE schemaname='public'");
                foreach ($rows as $row) {
                    $tables[] = $row->tablename;
                }
            }
        } catch (\Throwable $e) {
            // Ignorar errores y devolver lo que tengamos
        }
        return $tables;
    }

    private function getTableStats(?string $domain = null): array
    {
        $driver = config('database.default');
        $tables = $this->getTableNames();
        $stats = [];
        $sizeMap = [];
        $rowsMap = [];
        try {
            if ($driver === 'mysql') {
                $rows = DB::select("SELECT TABLE_NAME as table_name, (DATA_LENGTH + INDEX_LENGTH) as size_bytes, TABLE_ROWS as rows_count FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE()");
                foreach ($rows as $row) {
                    $sizeMap[$row->table_name] = (int) ($row->size_bytes ?? 0);
                    $rowsMap[$row->table_name] = (int) ($row->rows_count ?? 0);
                }
            } elseif ($driver === 'pgsql') {
                $rows = DB::select("SELECT relname as table_name, pg_total_relation_size(relid) as size_bytes FROM pg_catalog.pg_statio_user_tables");
                foreach ($rows as $row) {
                    $sizeMap[$row->table_name] = (int) ($row->size_bytes ?? 0);
                }
            } else {
                // sqlite: no hay tamaño por tabla fácilmente
            }
        } catch (\Throwable $e) {
            // Si falla, continuamos sin tamaños
        }

        foreach ($tables as $t) {
            $count = 0;
            $size = $sizeMap[$t] ?? null;
            $totalRows = $rowsMap[$t] ?? null;
            try {
                if ($domain) {
                    // Conteos por dominio para tablas clave
                    switch ($t) {
                        case 'accounts':
                            $count = Account::where('service_domain', $domain)->count();
                            break;
                        case 'admin_user_accounts':
                            // Contar relaciones donde el usuario pertenece al dominio
                            $count = AdminUserAccount::whereHas('user', function($q) use ($domain){
                                $q->where('email', 'like', '%@' . $domain);
                            })->count();
                            break;
                        case 'users':
                            $count = User::where('email', 'like', '%@' . $domain)->count();
                            break;
                        case 'invoices':
                            $count = Invoice::whereHas('user', function($q) use ($domain){
                                $q->where('email', 'like', '%@' . $domain);
                            })->count();
                            break;
                        case 'transactions':
                            $count = Transaction::whereHas('account', function($q) use ($domain){
                                $q->where('service_domain', $domain);
                            })->count();
                            break;
                        default:
                            // Por defecto, contar global
                            $count = DB::table($t)->count();
                            break;
                    }
                } else {
                    // Global
                    $count = DB::table($t)->count();
                }
            } catch (\Throwable $e) {
                // ignorar errores puntuales
            }

            // Estimar tamaño por dominio para tablas clave si tenemos totalRows y size
            $sizeHuman = null;
            if (!is_null($size)) {
                if ($domain && in_array($t, ['accounts','admin_user_accounts','users','invoices','transactions'], true) && $totalRows && $totalRows > 0) {
                    $estimated = (int) round($size * ($count / $totalRows));
                    $sizeHuman = $this->formatBytes($estimated);
                } else {
                    $sizeHuman = $this->formatBytes($size);
                }
            }

            $stats[] = [
                'name' => $t,
                'rows' => $count,
                'size' => $size,
                'size_human' => $sizeHuman,
            ];
        }
        return $stats;
    }

    private function getDatabaseSizeBytes(): int
    {
        $driver = config('database.default');
        try {
            if ($driver === 'mysql') {
                $row = DB::selectOne("SELECT SUM(DATA_LENGTH + INDEX_LENGTH) as size_bytes FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE()");
                return (int) ($row->size_bytes ?? 0);
            } elseif ($driver === 'sqlite') {
                $path = config('database.connections.sqlite.database');
                if ($path && is_file($path)) {
                    return (int) filesize($path);
                }
            } elseif ($driver === 'pgsql') {
                $row = DB::selectOne("SELECT pg_database_size(current_database()) as size_bytes");
                return (int) ($row->size_bytes ?? 0);
            }
        } catch (\Throwable $e) {
            // ignorar
        }
        return 0;
    }

    private function computeFileUsage(): array
    {
        $paths = [
            storage_path('app/public'),
            storage_path('app/private'),
        ];
        $categories = [
            'imagenes' => ['jpg','jpeg','png','gif','webp','svg','bmp','tiff'],
            'documentos' => ['pdf','doc','docx','xls','xlsx','csv','txt','rtf','odt','ods'],
            'archivos' => ['zip','rar','7z','tar','gz','bz2'],
            'audio' => ['mp3','wav','ogg','m4a','flac'],
            'video' => ['mp4','mkv','avi','mov','wmv','webm'],
        ];
        $usage = [];
        foreach (array_keys($categories) as $cat) {
            $usage[$cat] = ['bytes' => 0, 'count' => 0];
        }
        $usage['otros'] = ['bytes' => 0, 'count' => 0];

        foreach ($paths as $base) {
            if (!is_dir($base)) { continue; }
            $rii = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($base, \FilesystemIterator::SKIP_DOTS));
            foreach ($rii as $file) {
                if ($file->isFile()) {
                    $ext = strtolower($file->getExtension());
                    $bytes = (int) $file->getSize();
                    $matched = false;
                    foreach ($categories as $cat => $exts) {
                        if (in_array($ext, $exts, true)) {
                            $usage[$cat]['bytes'] += $bytes;
                            $usage[$cat]['count'] += 1;
                            $matched = true;
                            break;
                        }
                    }
                    if (!$matched) {
                        $usage['otros']['bytes'] += $bytes;
                        $usage['otros']['count'] += 1;
                    }
                }
            }
        }
        // Agregar representación humana
        foreach ($usage as $cat => $data) {
            $usage[$cat]['human'] = $this->formatBytes($data['bytes']);
        }
        return $usage;
    }

    private function computeFileUsageByDomain(?string $domain): array
    {
        // Sin dominio, devolver el global
        if (!$domain) {
            return $this->computeFileUsage();
        }

        $paths = [
            storage_path('app/public'),
            storage_path('app/private'),
        ];
        $categories = [
            'imagenes' => ['jpg','jpeg','png','gif','webp','svg','bmp','tiff'],
            'documentos' => ['pdf','doc','docx','xls','xlsx','csv','txt','rtf','odt','ods'],
            'archivos' => ['zip','rar','7z','tar','gz','bz2'],
            'audio' => ['mp3','wav','ogg','m4a','flac'],
            'video' => ['mp4','mkv','avi','mov','wmv','webm'],
        ];
        $usage = [];
        foreach (array_keys($categories) as $cat) {
            $usage[$cat] = ['bytes' => 0, 'count' => 0];
        }
        $usage['otros'] = ['bytes' => 0, 'count' => 0];

        foreach ($paths as $base) {
            if (!is_dir($base)) { continue; }
            $rii = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($base, \FilesystemIterator::SKIP_DOTS));
            foreach ($rii as $file) {
                if ($file->isFile()) {
                    $pathName = $file->getPathname();
                    // Heurística para detectar pertenencia al dominio
                    $belongs = str_contains($pathName, DIRECTORY_SEPARATOR . $domain . DIRECTORY_SEPARATOR)
                        || str_contains($pathName, '@' . $domain)
                        || str_contains($pathName, '_' . $domain . '_')
                        || str_contains($pathName, '-' . $domain . '-');
                    if (!$belongs) { continue; }

                    $ext = strtolower($file->getExtension());
                    $bytes = (int) $file->getSize();
                    $matched = false;
                    foreach ($categories as $cat => $exts) {
                        if (in_array($ext, $exts, true)) {
                            $usage[$cat]['bytes'] += $bytes;
                            $usage[$cat]['count'] += 1;
                            $matched = true;
                            break;
                        }
                    }
                    if (!$matched) {
                        $usage['otros']['bytes'] += $bytes;
                        $usage['otros']['count'] += 1;
                    }
                }
            }
        }
        foreach ($usage as $cat => $data) {
            $usage[$cat]['human'] = $this->formatBytes($data['bytes']);
        }
        return $usage;
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes <= 0) return '0 B';
        $units = ['B','KB','MB','GB','TB'];
        $pow = (int) floor(log($bytes, 1024));
        $pow = min($pow, count($units) - 1);
        $value = $bytes / pow(1024, $pow);
        return number_format($value, 2) . ' ' . $units[$pow];
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