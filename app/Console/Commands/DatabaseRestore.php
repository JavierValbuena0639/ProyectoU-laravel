<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DatabaseRestore extends Command
{
    protected $signature = 'db:restore {file : Nombre del archivo ZIP en storage/app/backups} {--keep : No truncar tablas antes de insertar}';
    protected $description = 'Restaura la base de datos desde un respaldo ZIP generado por db:auto-backup';

    public function handle(): int
    {
        $filename = $this->argument('file');
        if (!preg_match('/^[a-zA-Z0-9._-]+$/', $filename)) {
            $this->error('Nombre de archivo inválido.');
            return self::FAILURE;
        }

        $zipPath = storage_path('app/backups/' . $filename);
        if (!is_file($zipPath)) {
            $this->error('Respaldo no encontrado en: ' . $zipPath);
            return self::FAILURE;
        }

        $zip = new \ZipArchive();
        if ($zip->open($zipPath) !== true) {
            $this->error('No se pudo abrir el archivo ZIP.');
            return self::FAILURE;
        }

        $driver = config('database.default');
        $disableFk = function() use ($driver) {
            try {
                if ($driver === 'mysql') {
                    DB::statement('SET FOREIGN_KEY_CHECKS=0');
                } elseif ($driver === 'sqlite') {
                    DB::statement('PRAGMA foreign_keys = OFF');
                } elseif ($driver === 'pgsql') {
                    DB::statement("SET session_replication_role = 'replica'");
                }
            } catch (\Throwable $e) {}
        };
        $enableFk = function() use ($driver) {
            try {
                if ($driver === 'mysql') {
                    DB::statement('SET FOREIGN_KEY_CHECKS=1');
                } elseif ($driver === 'sqlite') {
                    DB::statement('PRAGMA foreign_keys = ON');
                } elseif ($driver === 'pgsql') {
                    DB::statement("SET session_replication_role = 'origin'");
                }
            } catch (\Throwable $e) {}
        };

        $keep = (bool) $this->option('keep');

        $tablesRestored = 0;
        DB::beginTransaction();
        $disableFk();
        try {
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $entry = $zip->getNameIndex($i);
                if (!str_ends_with($entry, '.json')) { continue; }
                $table = basename($entry, '.json');
                $json = $zip->getFromIndex($i);
                if ($json === false) { continue; }
                $rows = json_decode($json, true);
                if (!is_array($rows)) { continue; }

                if (!$keep) {
                    try { DB::table($table)->truncate(); } catch (\Throwable $e) {
                        // algunas tablas pueden no soportar truncate; usar delete
                        try { DB::table($table)->delete(); } catch (\Throwable $e2) {}
                    }
                }

                // Insertar por lotes
                $batch = [];
                $batchSize = 500;
                foreach ($rows as $row) {
                    if (!is_array($row)) { $row = (array) $row; }
                    $batch[] = $row;
                    if (count($batch) >= $batchSize) {
                        DB::table($table)->insert($batch);
                        $batch = [];
                    }
                }
                if ($batch) { DB::table($table)->insert($batch); }
                $tablesRestored++;
                $this->info("Tabla restaurada: {$table} (" . count($rows) . " filas)");
            }

            $zip->close();
            $enableFk();
            DB::commit();
            try {
                Log::channel('audit')->info('Database restored', [
                    'file' => $filename,
                    'tables' => $tablesRestored,
                ]);
            } catch (\Throwable $e) {}
            $this->info('Restauración completa. Tablas procesadas: ' . $tablesRestored);
            return self::SUCCESS;
        } catch (\Throwable $e) {
            $zip->close();
            $enableFk();
            DB::rollBack();
            try {
                Log::channel('audit')->error('Database restore error', [
                    'file' => $filename,
                    'error' => $e->getMessage(),
                ]);
            } catch (\Throwable $e2) {}
            $this->error('Error en restauración: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}