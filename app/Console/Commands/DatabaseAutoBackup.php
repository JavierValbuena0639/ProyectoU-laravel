<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DatabaseAutoBackup extends Command
{
    protected $signature = 'db:auto-backup';
    protected $description = 'Crear respaldo automático de la base de datos (JSON por tabla en ZIP)';

    public function handle(): int
    {
        $backupDir = storage_path('app/backups');
        if (!is_dir($backupDir)) {
            @mkdir($backupDir, 0775, true);
        }

        $filename = 'backup_' . date('Y_m_d_His') . '_auto.zip';
        $zipPath = $backupDir . DIRECTORY_SEPARATOR . $filename;

        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE) !== true) {
            $this->error('No se pudo crear el archivo de respaldo.');
            Log::channel('audit')->error('Auto backup create error', ['error' => 'zip open failed']);
            return self::FAILURE;
        }

        $tables = $this->getTableNames();
        foreach ($tables as $table) {
            try {
                $rows = DB::table($table)->get();
                $zip->addFromString($table . '.json', json_encode($rows, JSON_PRETTY_PRINT));
            } catch (\Throwable $e) {
                // continuar si una tabla falla
            }
        }

        $zip->close();

        Log::channel('audit')->info('Auto backup created', [
            'file' => $filename,
            'tables_count' => count($tables),
        ]);
        $this->info('Respaldo automático creado: ' . $filename);
        return self::SUCCESS;
    }

    private function getTableNames(): array
    {
        $driver = config('database.default');
        $tables = [];
        try {
            if ($driver === 'mysql') {
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
            // ignorar
        }
        return $tables;
    }
}