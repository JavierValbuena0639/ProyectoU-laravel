<?php

namespace App\Console\Commands;

use App\Jobs\QueueBenchmarkJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class QueueBenchmarkCommand extends Command
{
    protected $signature = 'app:queue-benchmark {--n=200 : Número de jobs} {--queue=default : Nombre de la cola} {--timeout=30 : Tiempo máximo en segundos}';
    protected $description = 'Benchmark básico de colas: encola N jobs y espera su procesamiento (a través de Redis)';

    public function handle(): int
    {
        $n = (int) $this->option('n');
        $queue = (string) $this->option('queue');
        $timeout = (int) $this->option('timeout');

        try {
            // Forzar conexión redis para el benchmark (no persistente)
            config(['queue.default' => 'redis']);
            // Reset contador
            Redis::del('queue_benchmark:processed');

            $this->info("Encolando {$n} jobs en cola '{$queue}'...");
            $t0 = microtime(true);

            for ($i = 0; $i < $n; $i++) {
                QueueBenchmarkJob::dispatch()->onQueue($queue);
            }

            $deadline = $t0 + $timeout;
            $processed = 0;

            while (microtime(true) < $deadline) {
                $processed = (int) (Redis::get('queue_benchmark:processed') ?? 0);
                if ($processed >= $n) {
                    break;
                }
                usleep(200000); // 200ms
            }

            $elapsed = microtime(true) - $t0;

            logger()->channel('metrics')->info('queue_benchmark', [
                'n' => $n,
                'processed' => $processed,
                'seconds' => round($elapsed, 3),
                'throughput_s' => $elapsed > 0 ? round($processed / $elapsed, 2) : null,
                'queue' => $queue,
            ]);

            if ($processed < $n) {
                $this->warn("Timeout: procesados {$processed}/{$n} en " . number_format($elapsed, 2) . "s");
                $this->line("Asegúrate de ejecutar el worker: php artisan queue:work --queue={$queue} --sleep=0 --timeout=60");
                return self::FAILURE;
            }

            $this->info("Completado: {$processed} jobs en " . number_format($elapsed, 2) . "s (" . number_format($processed / max($elapsed, 0.0001), 2) . " jobs/s)");
            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Error en queue-benchmark: ' . $e->getMessage());
            logger()->channel('metrics')->error('queue_benchmark_error', [
                'error' => $e->getMessage(),
            ]);
            return self::FAILURE;
        }
    }
}