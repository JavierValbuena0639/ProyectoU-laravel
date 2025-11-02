<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Support\Totp;
use App\Models\Audit;

class TwoFactorLoginController extends Controller
{
    /**
     * Mostrar el desafío de 2FA para completar el acceso
     */
    public function show(Request $request)
    {
        $userId = $request->session()->get('two_factor:user_id');
        if (!$userId) {
            return redirect()->route('login')->with('error', 'Primero inicia sesión para continuar.');
        }
        $user = User::find($userId);
        if (!$user || !$user->two_factor_enabled || empty($user->two_factor_secret)) {
            // Estado inconsistente; volver al login
            $request->session()->forget(['two_factor:user_id', 'two_factor:remember']);
            return redirect()->route('login')->with('error', 'No hay un desafío de 2FA activo.');
        }

        return view('auth.twofa-challenge', [
            'user' => $user,
        ]);
    }

    /**
     * Verificar el código 2FA y completar el login
     */
    public function verify(Request $request)
    {
        $request->validate([
            'code' => ['required','digits:6'],
        ]);
        $userId = $request->session()->get('two_factor:user_id');
        $remember = (bool) $request->session()->get('two_factor:remember', false);

        $user = $userId ? User::find($userId) : null;
        if (!$user) {
            return redirect()->route('login')->with('error', 'Sesión expirada. Inicia nuevamente.');
        }
        if (!$user->active) {
            throw ValidationException::withMessages([
                'code' => ['Tu cuenta está desactivada y no puede acceder.'],
            ]);
        }
        if (!$user->two_factor_enabled || empty($user->two_factor_secret)) {
            return redirect()->route('login')->with('error', '2FA no está activo para esta cuenta.');
        }

        $code = $request->input('code');
        $ok = Totp::verify($user->two_factor_secret, $code);
        if (!$ok) {
            throw ValidationException::withMessages([
                'code' => ['Código inválido. Intenta nuevamente.'],
            ]);
        }

        // Completar login y limpiar marcadores de 2FA
        $request->session()->forget(['two_factor:user_id', 'two_factor:remember']);
        Auth::login($user, $remember);
        $request->session()->regenerate();
        $user->update(['last_login' => now()]);

        // Auditoría: verificación 2FA exitosa
        try {
            Audit::create([
                'user_id' => $user->id,
                'event' => 'twofa_success',
                'auditable_type' => 'User',
                'auditable_id' => $user->id,
                'ip_address' => $request->ip(),
                'user_agent' => (string) $request->header('User-Agent'),
                'url' => $request->fullUrl(),
                'description' => 'Verificación de segundo factor exitosa',
            ]);
        } catch (\Throwable $e) {}

        if ($user->isAdmin()) {
            return redirect()->intended('/admin/dashboard');
        }
        if (method_exists($user, 'isSupport') && $user->isSupport()) {
            return redirect()->intended('/admin/database');
        }
        return redirect()->intended('/dashboard');
    }
}