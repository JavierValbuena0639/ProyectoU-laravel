<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerificationCodeMail;
use Illuminate\Validation\Rules\Password;
use App\Rules\NotPublicEmailDomain;

class RegisterController extends Controller
{
    /**
     * Mostrar formulario de registro de administrador
     */
    public function show()
    {
        return view('auth.register');
    }

    /**
     * Registrar nuevo administrador
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required','email','unique:users,email', new NotPublicEmailDomain()],
            'password' => ['required', 'string', Password::min(8)->letters()->mixedCase()->numbers()->symbols()->uncompromised(), 'confirmed'],
        ]);

        $adminRole = Role::where('name', 'admin')->first();
        if (!$adminRole) {
            return back()->withErrors(['role' => 'No se encontró el rol de administrador. Ejecuta los seeders.']);
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role_id' => $adminRole->id,
            'active' => true,
            // Enviar código por correo; mantener verificación pendiente
            'email_verified_at' => null,
        ]);

        // Generar y enviar código verificador por correo
        try {
            $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            Mail::to($user->email)->send(new VerificationCodeMail($user, $code));
            // Mensaje informativo para el administrador recién registrado
            session()->flash('status', 'Hemos enviado un código de verificación al correo ' . $user->email . '.');
        } catch (\Throwable $e) {
            // No bloquear el flujo si el correo falla; mostrar aviso
            session()->flash('error', 'No fue posible enviar el correo de verificación: ' . $e->getMessage());
        }

        Auth::login($user);

        return redirect()->route('admin.dashboard');
    }
}