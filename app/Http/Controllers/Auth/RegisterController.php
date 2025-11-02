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
            // OTP aleatorio: 6 dígitos
            $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            Mail::to($user->email)->send(new VerificationCodeMail($user, $code));
            $user->forceFill([
                'verification_code' => $code,
                'verification_code_sent_at' => now(),
            ])->save();
        } catch (\Throwable $e) {
            session()->flash('error', 'No fue posible enviar el correo de verificación: ' . $e->getMessage());
        }

        Auth::login($user);

        return redirect()->route('admin.dashboard');
    }
    protected function create(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        try {
            // Enviar OTP aleatorio de 6 dígitos en el primer envío
            $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            Mail::to($user->email)->send(new VerificationCodeMail($user, $code));
            $user->forceFill([
                'verification_code' => $code,
                'verification_code_sent_at' => now(),
            ])->save();
        } catch (\Throwable $e) {
            // Log::warning('No se pudo enviar el código de verificación: '.$e->getMessage());
        }

        return $user;
    }
}