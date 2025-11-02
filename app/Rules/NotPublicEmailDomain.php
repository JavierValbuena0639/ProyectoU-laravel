<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class NotPublicEmailDomain implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  string  $attribute
     * @param  mixed   $value
     * @param  \Closure(string): void  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $domain = '';
        if (is_string($value) && str_contains($value, '@')) {
            $parts = explode('@', trim($value));
            $domain = strtolower($parts[1] ?? '');
        }

        if ($domain === '') {
            return; // Otras reglas (email) manejarán formato vacío/incorrecto
        }

        $blocked = (array) config('security.blocked_email_domains', []);
        // Normalizar espacios y mayúsculas en la lista
        $blocked = array_map(fn($d) => strtolower(trim((string) $d)), $blocked);

        if (in_array($domain, $blocked, true)) {
            $fail('No se permite usar el dominio de correo "' . $domain . '". Usa un correo corporativo (no público).');
        }
    }
}