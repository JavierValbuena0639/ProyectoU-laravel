<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureEmailCodeVerified
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // Rutas exentas de verificación
        $exempt = [
            'login', 'logout', 'register', 'password.reset', 'password.update', 'password.email', 'password.forgot_admin',
            'locale.switch', 'auth.verify.show', 'auth.verify.submit'
        ];
        $currentName = optional($request->route())->getName();
        if (in_array($currentName, $exempt, true)) {
            return $next($request);
        }

        if ($user && is_null($user->email_verified_at)) {
            // Forzar a la pantalla de verificación si no está verificado
            return redirect()->route('auth.verify.show')
                ->with('status', 'Debes verificar tu correo ingresando el código enviado antes de continuar.');
        }

        return $next($request);
    }
}