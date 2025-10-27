<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\DatabaseAutoBackup::class,
        \App\Console\Commands\DatabaseRestore::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('db:auto-backup')
            ->dailyAt('02:00')
            ->when(function () {
                return filter_var(env('DB_AUTO_BACKUP', false), FILTER_VALIDATE_BOOLEAN);
            });
    }

    /**
     * Register the commands for the application.
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        // if (file_exists(base_path('routes/console.php'))) {
        //     require base_path('routes/console.php');
        // }
    }
}