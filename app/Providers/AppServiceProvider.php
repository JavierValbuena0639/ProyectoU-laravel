<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

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
    }
}
