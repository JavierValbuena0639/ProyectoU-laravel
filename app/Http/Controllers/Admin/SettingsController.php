<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class SettingsController extends Controller
{
    public function save(Request $request)
    {
        $request->validate([
            'timezone' => 'required|string',
            'date_format' => 'required|string',
            'language' => 'required|in:es,en',
        ]);

        $language = $request->input('language');
        $fakerLocale = $language === 'en' ? 'en_US' : 'es_CO';

        $data = [
            'APP_TIMEZONE' => $request->input('timezone'),
            'APP_LOCALE' => $language,
            'APP_FAKER_LOCALE' => $fakerLocale,
            'APP_DATE_FORMAT' => $request->input('date_format'),
        ];

        $this->writeEnv($data);

        // Aplicar en runtime para que el cambio de idioma/fecha se refleje inmediatamente
        App::setLocale($language);
        Carbon::setLocale($language);
        Session::put('app_locale', $language);
        $tz = (string) $request->input('timezone');
        if (!empty($tz)) {
            @date_default_timezone_set($tz);
            Config::set('app.timezone', $tz);
        }
        $df = (string) $request->input('date_format');
        if (!empty($df)) {
            Config::set('app.date_format', $df);
        }

        return back()->with('success', __('Configuración del sistema actualizada correctamente'));
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