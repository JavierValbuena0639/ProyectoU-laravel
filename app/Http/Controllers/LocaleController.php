<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App as AppFacade;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cookie;
use Carbon\Carbon;

class LocaleController extends Controller
{
    public function switch(Request $request, string $lang)
    {
        // Validar idiomas soportados
        if (!in_array($lang, ['es', 'en'])) {
            $lang = 'es';
        }

        // Aplicar inmediatamente en la sesión actual
        AppFacade::setLocale($lang);
        Carbon::setLocale($lang);
        Session::put('app_locale', $lang);

        // Persistir en cookie para mantener preferencia entre sesiones (1 año)
        // 60 minutos * 24 horas * 365 días
        Cookie::queue('app_locale', $lang, 60 * 24 * 365);

        // Redirección: volver siempre a la página anterior si existe; de lo contrario, a '/'.
        $referer = (string) $request->headers->get('referer');
        $path = '/';
        if (!empty($referer)) {
            $parsedPath = parse_url($referer, PHP_URL_PATH);
            if (is_string($parsedPath) && $parsedPath !== '') {
                $path = $parsedPath;
            }
        }
        return redirect($path);
    }
}