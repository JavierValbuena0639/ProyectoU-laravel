<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App as AppFacade;
use Illuminate\Support\Facades\Session;
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