<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\VerificationCodeMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class VerificationController extends Controller
{
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

        $input = $request->input('code');
        $daily = now()->format('ymd');

        // Si hay OTP almacenado, validar TTL y coincidencia
        $stored = $user->verification_code;
        $sentAt = $user->verification_code_sent_at;
        if ($stored) {
            if ($sentAt && now()->diffInMinutes($sentAt) > 10) {
                // OTP expirado
                return back()->withErrors(['code' => 'El código ha expirado. Solicita un nuevo código.']);
            }
            if ($stored === $input) {
                $user->forceFill([
                    'email_verified_at' => now(),
                    'verification_code' => null,
                    'verification_code_sent_at' => null,
                ])->save();
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
            return redirect()->route('dashboard')->with('success', 'Correo verificado correctamente.');
        }

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
            return back()->with('status', 'Hemos reenviado un nuevo código a tu correo.');
        } catch (\Throwable $e) {
            return back()->withErrors(['code' => 'No fue posible reenviar el código: ' . $e->getMessage()]);
        }
    }
}