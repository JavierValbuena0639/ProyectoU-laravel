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

        // Redirección amigable: volver a la página anterior excepto login/register.
        // Si no hay referer o proviene de login/register, ir a '/'.
        $referer = (string) $request->headers->get('referer');
        if (!empty($referer)) {
            $path = parse_url($referer, PHP_URL_PATH) ?? '/';
            if (!in_array($path, ['/login', '/register'])) {
                return redirect($path);
            }
        }
        return redirect('/');
    }
}