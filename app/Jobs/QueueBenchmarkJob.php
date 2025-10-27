<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Redis;

class QueueBenchmarkJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;
    public $timeout = 30;

    public function __construct()
    {
        // No payload needed
    }

    public function handle(): void
    {
        try {
            // Simular trabajo ligero
            usleep(10000); // 10ms

            // Incrementar contador de procesados en Redis
            Redis::incr('queue_benchmark:processed');
        } catch (\Throwable $e) {
            // Evitar fallos silenciosos; registrar si es necesario
            logger()->channel('metrics')->warning('queue_benchmark_job_error', [
                'error' => $e->getMessage(),
            ]);
        }
    }
}