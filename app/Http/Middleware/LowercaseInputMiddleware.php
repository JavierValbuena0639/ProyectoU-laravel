<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LowercaseInputMiddleware
{
    /**
     * Convierte todos los valores string del request a minúsculas
     * antes de llegar al controlador. Excluye contraseñas.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $excludedKeys = ['_token', 'password', 'password_confirmation', 'confirm_word'];

        $data = $request->all();
        $normalized = $this->lowercaseRecursive($data, $excludedKeys);
        $request->merge($normalized);

        return $next($request);
    }

    /**
     * Recorre el arreglo/valor de forma recursiva: si es string, lo pone en minúsculas;
     * si es arreglo, repite el proceso; otros tipos se devuelven tal cual.
     */
    private function lowercaseRecursive($value, array $excludedKeys)
    {
        if (is_array($value)) {
            $result = [];
            foreach ($value as $key => $val) {
                if (in_array($key, $excludedKeys, true)) {
                    $result[$key] = $val;
                } else {
                    $result[$key] = $this->lowercaseRecursive($val, $excludedKeys);
                }
            }
            return $result;
        }

        if (is_string($value)) {
            return mb_strtolower($value, 'UTF-8');
        }

        return $value;
    }
}