<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\User;
use App\Observers\UserObserver;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Establecer locale priorizando sesión y luego configuración/env
        $locale = (string) (Session::get('app_locale') ?? Config::get('app.locale') ?? 'es');
        $timezone = (string) (Config::get('app.timezone') ?? 'America/Bogota');
        App::setLocale($locale);
        date_default_timezone_set($timezone);
        Carbon::setLocale($locale);

        // Directiva Blade para formateo de moneda con conversión opcional
        \Illuminate\Support\Facades\Blade::directive('money', function ($expression) {
            // Uso: @money(valor, monedaDestino?)
            return "<?php echo \\App\\Support\\Currency::format($expression); ?>";
        });

        // Directiva Blade para formateo de fecha según APP_DATE_FORMAT
        \Illuminate\Support\Facades\Blade::directive('fecha', function ($expression) {
            // Uso: @fecha(fecha)
            $format = (string) (Config::get('app.date_format') ?? env('APP_DATE_FORMAT', 'd/m/Y'));
            return "<?php echo \Carbon\Carbon::parse($expression)->translatedFormat('{$format}'); ?>";
        });

        // Respaldo automático SOLO la primera vez que se inicializa el servicio
        $enabled = filter_var(env('DB_AUTO_BACKUP', false), FILTER_VALIDATE_BOOLEAN);
        if ($enabled) {
            $markerPath = storage_path('app/backups/.boot_init_done');
            if (!File::exists($markerPath)) {
                // Evitar concurrencia entre procesos en el arranque
                if (Cache::add('auto_backup_boot_once_lock', time(), now()->addMinutes(15))) {
                    try {
                        Artisan::call('db:auto-backup');
                        File::put($markerPath, Carbon::now()->toDateTimeString());
                        Log::channel('audit')->info('Auto backup on first service init');
                    } catch (\Throwable $e) {
                        try { Log::channel('audit')->error('Auto backup on first init error', ['error' => $e->getMessage()]); } catch (\Throwable $e2) {}
                    }
                }
            }
        }

        // Registrar observador para mantener email_domain actualizado
        try {
            User::observe(UserObserver::class);
        } catch (\Throwable $e) {
            // evitar bloquear el boot en caso de error temprano de clases
        }
    }
}
