<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminOrSupportMiddleware
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
        $user = Auth::user();
        if (!$user->isAdminOrSupport()) {
            abort(403, 'Se requiere rol administrador o soporte interno.');
=======
        // Permitir acceso si es admin o soporte
        $user = Auth::user();
        if (!($user->isAdmin() || (method_exists($user, 'isSupport') && $user->isSupport()))) {
            abort(403, 'No tienes permisos para acceder a esta sección.');
>>>>>>> Stashed changes
        }

        return $next($request);
    }
}