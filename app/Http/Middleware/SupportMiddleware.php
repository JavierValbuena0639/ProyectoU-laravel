<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SupportMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
<<<<<<< Updated upstream
=======
        // Verificar si el usuario está autenticado
>>>>>>> Stashed changes
        if (!Auth::check()) {
            return redirect()->route('login');
        }

<<<<<<< Updated upstream
        if (!Auth::user()->isSupport()) {
            abort(403, 'Acceso restringido a soporte interno.');
=======
        // Verificar si el usuario es soporte interno
        if (!Auth::user()->isSupport()) {
            abort(403, 'No tienes permisos para acceder a esta sección.');
>>>>>>> Stashed changes
        }

        return $next($request);
    }
}