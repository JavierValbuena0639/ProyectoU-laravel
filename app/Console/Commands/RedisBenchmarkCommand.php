<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class RedisBenchmarkCommand extends Command
{
    protected $signature = 'app:redis-benchmark {--n=5000 : Número de operaciones}';
    protected $description = 'Ejecuta un benchmark básico de Redis (ping, set/get, pipeline)';

    public function handle(): int
    {
        try {
            // Ping
            $t0 = microtime(true);
            $pong = Redis::ping();
            $pingMs = (microtime(true) - $t0) * 1000;
            $this->info("PING: {$pong} en " . number_format($pingMs, 2) . " ms");

            $n = (int) $this->option('n');
            $this->info("Benchmark set/get simple (n={$n})...");

            // Set/Get simple
            $t1 = microtime(true);
            for ($i = 0; $i < $n; $i++) {
                Redis::set("bench:s:$i", (string) $i);
                Redis::get("bench:s:$i");
            }
            $sgSecs = microtime(true) - $t1;

            // Pipeline set
            $this->info('Benchmark pipeline set/get...');
            $t2 = microtime(true);
            Redis::pipeline(function ($pipe) use ($n) {
                for ($i = 0; $i < $n; $i++) {
                    $pipe->set("bench:p:$i", (string) $i);
                }
            });
            Redis::pipeline(function ($pipe) use ($n) {
                for ($i = 0; $i < $n; $i++) {
                    $pipe->get("bench:p:$i");
                }
            });
            $plSecs = microtime(true) - $t2;

            // Limpieza
            Redis::pipeline(function ($pipe) use ($n) {
                for ($i = 0; $i < $n; $i++) {
                    $pipe->del("bench:s:$i");
                    $pipe->del("bench:p:$i");
                }
            });

            $this->line(sprintf("Set/Get simple: %.2fs (%.0f ops/s)", $sgSecs, $n * 2 / max($sgSecs, 0.0001)));
            $this->line(sprintf("Pipeline set/get: %.2fs (%.0f ops/s)", $plSecs, $n * 2 / max($plSecs, 0.0001)));

            logger()->channel('metrics')->info('redis_benchmark', [
                'ping_ms' => round($pingMs, 2),
                'n' => $n,
                'simple_seconds' => round($sgSecs, 4),
                'pipeline_seconds' => round($plSecs, 4),
            ]);

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Error en benchmark Redis: ' . $e->getMessage());
            logger()->channel('metrics')->error('redis_benchmark_error', [
                'error' => $e->getMessage(),
            ]);
            return self::FAILURE;
        }
    }
}