<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        if ($user->verification_code === $request->input('code')) {
            $user->forceFill([
                'email_verified_at' => now(),
                'verification_code' => null,
                'verification_code_sent_at' => null,
            ])->save();
            return redirect()->route('dashboard')->with('success', 'Correo verificado correctamente.');
        }

        return back()->withErrors(['code' => 'Código inválido. Verifica el correo e inténtalo nuevamente.']);
    }
}