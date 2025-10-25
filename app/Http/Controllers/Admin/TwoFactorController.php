<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Support\Totp;
use Illuminate\Support\Carbon;

class TwoFactorController extends Controller
{
    /**
     * Mostrar gestión de 2FA para un usuario
     */
    public function show(User $user)
    {
        $this->authorizeAdmin();

        if (!$user->two_factor_secret) {
            $user->two_factor_secret = Totp::generateSecret();
            $user->two_factor_enabled = false;
            $user->two_factor_confirmed_at = null;
            $user->save();
        }

        $issuer = config('app.name', 'SumAxia');
        $otpauth = Totp::otpauthUri($issuer, $user->email, $user->two_factor_secret);
        $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . urlencode($otpauth);

        return view('admin.users-twofa', [
            'user' => $user,
            'qrUrl' => $qrUrl,
            'otpauth' => $otpauth,
        ]);
    }

    /**
     * Verificar código 2FA y activar
     */
    public function verify(Request $request, User $user)
    {
        $this->authorizeAdmin();

        $request->validate([
            'code' => ['required','digits:6']
        ]);

        $code = $request->input('code');
        $ok = Totp::verify($user->two_factor_secret ?? '', $code);
        if (!$ok) {
            return back()->withErrors(['code' => 'Código inválido. Intenta nuevamente.']);
        }
        $user->two_factor_enabled = true;
        $user->two_factor_confirmed_at = Carbon::now();
        $user->save();

        return redirect()->route('admin.users.2fa', $user)->with('success', '2FA activado para el usuario.');
    }

    /**
     * Desactivar 2FA, limpiar secreto
     */
    public function disable(User $user)
    {
        $this->authorizeAdmin();

        $user->two_factor_enabled = false;
        $user->two_factor_confirmed_at = null;
        $user->two_factor_secret = null;
        $user->save();

        return redirect()->route('admin.users.2fa', $user)->with('success', '2FA desactivado.');
    }

    private function authorizeAdmin(): void
    {
        $auth = auth()->user();
        if (!$auth || !$auth->isAdmin()) {
            abort(403);
        }
    }
}