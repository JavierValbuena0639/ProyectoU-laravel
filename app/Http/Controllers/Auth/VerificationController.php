<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\VerificationCodeMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Cache\RateLimiter;
use Illuminate\Support\Str;
use App\Models\Audit;

class VerificationController extends Controller
{
    /**
     * Estado de verificación: si puede reenviar y tiempo restante de cooldown.
     */
    public function status(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        /** @var RateLimiter $limiter */
        $limiter = app(RateLimiter::class);
        $key = 'verify:resend:'.($user->id ?? 'guest').'|'.$request->ip();
        $maxResends = 3;          // máximo reenvíos por ventana
        $decaySeconds = 15 * 60;  // ventana de 15 minutos

        $rateLimited = $limiter->tooManyAttempts($key, $maxResends);
        $rateLimitRemainingSeconds = $rateLimited ? $limiter->availableIn($key) : 0;

        $lastSent = $user->verification_code_sent_at;
        $cooldown = 60; // segundos
        $cooldownRemainingSeconds = 0;
        if ($lastSent) {
            $elapsed = now()->diffInSeconds($lastSent);
            if ($elapsed < $cooldown) {
                $cooldownRemainingSeconds = $cooldown - $elapsed;
            }
        }

        $alreadyVerified = !is_null($user->email_verified_at);
        $canResend = !$alreadyVerified && !$rateLimited && ($cooldownRemainingSeconds === 0);

        return response()->json([
            'can_resend' => $canResend,
            'cooldown_remaining_seconds' => $cooldownRemainingSeconds,
            'rate_limited' => $rateLimited,
            'rate_limit_remaining_seconds' => $rateLimitRemainingSeconds,
            'already_verified' => $alreadyVerified,
        ]);
    }
    /**
     * Mostrar formulario para ingresar código.
     */
    public function show()
    {
        return view('auth.verify-code');
    }

    /**
     * Validar código de verificación y marcar email como verificado.
     */
    public function verify(Request $request)
    {
        $request->validate([
            'code' => ['required','string','size:6'],
        ]);

        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        /** @var RateLimiter $limiter */
        $limiter = app(RateLimiter::class);
        $key = 'verify:submit:'.($user->id ?? 'guest').'|'.$request->ip();
        $maxAttempts = 10;        // máximo intentos de verificación por ventana
        $decaySeconds = 15 * 60;  // ventana de 15 minutos

        if ($limiter->tooManyAttempts($key, $maxAttempts)) {
            $seconds = $limiter->availableIn($key);
            return back()->withErrors(['code' => 'Demasiadas solicitudes. Intenta nuevamente en ' . $seconds . ' segundos.']);
        }

        $input = $request->input('code');
        $daily = now()->format('ymd');

        // Si hay OTP almacenado, validar TTL y coincidencia
        $stored = $user->verification_code;
        $sentAt = $user->verification_code_sent_at;
        if ($stored) {
            if ($sentAt && now()->diffInMinutes($sentAt) > 10) {
                // OTP expirado
                $limiter->hit($key, $decaySeconds);
                return back()->withErrors(['code' => 'El código ha expirado. Solicita un nuevo código.']);
            }
            if ($stored === $input) {
                $user->forceFill([
                    'email_verified_at' => now(),
                    'verification_code' => null,
                    'verification_code_sent_at' => null,
                ])->save();
                // limpiar contador al éxito
                $limiter->clear($key);
                try {
                    Audit::create([
                        'user_id' => $user->id,
                        'event' => 'email_verification_success',
                        'auditable_type' => 'User',
                        'auditable_id' => $user->id,
                        'ip_address' => $request->ip(),
                        'user_agent' => (string) $request->header('User-Agent'),
                        'url' => $request->fullUrl(),
                        'description' => 'Verificación de correo exitosa mediante código enviado',
                    ]);
                } catch (\Throwable $e) {}
                return redirect()->route('dashboard')->with('success', 'Correo verificado correctamente.');
            }
        }

        // Aceptar código diario por defecto (sin requerir envío previo)
        if ($input === $daily) {
            $user->forceFill([
                'email_verified_at' => now(),
                'verification_code' => null,
                'verification_code_sent_at' => null,
            ])->save();
            $limiter->clear($key);
            try {
                Audit::create([
                    'user_id' => $user->id,
                    'event' => 'email_verification_success',
                    'auditable_type' => 'User',
                    'auditable_id' => $user->id,
                    'ip_address' => $request->ip(),
                    'user_agent' => (string) $request->header('User-Agent'),
                    'url' => $request->fullUrl(),
                    'description' => 'Verificación de correo exitosa mediante código diario',
                ]);
            } catch (\Throwable $e) {}
            return redirect()->route('dashboard')->with('success', 'Correo verificado correctamente.');
        }

        // intento fallido
        $limiter->hit($key, $decaySeconds);
        return back()->withErrors(['code' => 'Código inválido. Verifica el correo e inténtalo nuevamente.']);
    }

    /**
     * Reenviar nuevo código de verificación al usuario autenticado.
     */
    public function resend(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        if (!is_null($user->email_verified_at)) {
            return redirect()->route('dashboard')->with('success', 'Tu correo ya está verificado.');
        }

        /** @var RateLimiter $limiter */
        $limiter = app(RateLimiter::class);
        $key = 'verify:resend:'.($user->id ?? 'guest').'|'.$request->ip();
        $maxResends = 3;          // máximo reenvíos por ventana
        $decaySeconds = 15 * 60;  // ventana de 15 minutos

        if ($limiter->tooManyAttempts($key, $maxResends)) {
            $seconds = $limiter->availableIn($key);
            return back()->with('status', 'Demasiadas solicitudes. Intenta nuevamente en ' . $seconds . ' segundos.');
        }

        // Enfriamiento: permitir un reenvío cada 60 segundos
        $lastSent = $user->verification_code_sent_at;
        if ($lastSent) {
            $elapsed = now()->diffInSeconds($lastSent);
            $cooldown = 60;
            if ($elapsed < $cooldown) {
                $remaining = $cooldown - $elapsed;
                return back()->with('status', 'Por favor espera ' . $remaining . ' segundos para solicitar otro código.');
            }
        }

        try {
            // OTP aleatorio de 6 dígitos
            $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            Mail::to($user->email)->send(new VerificationCodeMail($user, $code));
            $user->forceFill([
                'verification_code' => $code,
                'verification_code_sent_at' => now(),
            ])->save();
            // contar este reenvío en la ventana
            $limiter->hit($key, $decaySeconds);
            return back()->with('status', 'Hemos reenviado un nuevo código a tu correo.');
        } catch (\Throwable $e) {
            return back()->withErrors(['code' => 'No fue posible reenviar el código: ' . $e->getMessage()]);
        }
    }
}