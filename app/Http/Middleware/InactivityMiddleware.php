<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class InactivityMiddleware
{
    /**
     * Expira la sesión si hay 5 minutos de inactividad.
     * No afecta a usuarios no autenticados.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $last = (int) $request->session()->get('last_activity_time', 0);
            $now = time();
            $timeoutSeconds = 5 * 60; // 5 minutos

            if ($last > 0 && ($now - $last) > $timeoutSeconds) {
                // Cerrar sesión por inactividad y redirigir a login
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect('/login')->with('status', 'Sesión expirada por inactividad');
            }

            // Actualizar marca de tiempo
            $request->session()->put('last_activity_time', $now);
        }

        return $next($request);
    }
}