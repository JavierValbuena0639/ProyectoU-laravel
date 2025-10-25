<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Password;

class LoginController extends Controller
{
    /**
     * Mostrar el formulario de login
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Procesar el login del usuario
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        $remember = $request->boolean('remember');
        $email = $request->input('email');
        $password = $request->input('password');

        $user = User::where('email', $email)->first();
        if (!$user || !Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Las credenciales proporcionadas no coinciden con nuestros registros.'],
            ]);
        }

        // Bloquear acceso si el usuario está inactivo
        if (!$user->active) {
            throw ValidationException::withMessages([
                'email' => ['Tu cuenta está desactivada y no puede acceder.'],
            ]);
        }

        // Si el usuario tiene 2FA activo, solicitar desafío antes de completar login
        if ($user->two_factor_enabled) {
            $request->session()->put('two_factor:user_id', $user->id);
            $request->session()->put('two_factor:remember', $remember);
            return redirect()->route('auth.twofa.show')->with('status', 'Introduce el código de 6 dígitos para completar el acceso.');
        }

        // Login normal (sin 2FA)
        Auth::login($user, $remember);
        $request->session()->regenerate();

        // Actualizar último login
        $user->update([
            'last_login' => now()
        ]);

        // Redirigir según el rol del usuario
        if ($user->isAdmin()) {
            return redirect()->intended('/admin/dashboard');
        }
        if (method_exists($user, 'isSupport') && $user->isSupport()) {
            return redirect()->intended('/admin/database');
        }
        
        return redirect()->intended('/dashboard');
    }

    /**
     * Cerrar sesión del usuario
     */
    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/login');
    }

    /**
     * Enviar enlace de restablecimiento de contraseña al correo del administrador
     */
    public function sendAdminResetLink(Request $request)
    {
        // Buscar primer administrador activo
        $admin = User::whereHas('role', function ($q) {
                $q->where('name', 'admin');
            })
            ->where('active', true)
            ->orderBy('id')
            ->first();

        if (!$admin) {
            return back()->with('error', 'No existe un usuario administrador activo para recuperar la contraseña.');
        }

        $status = Password::sendResetLink(['email' => $admin->email]);

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('status', 'Hemos enviado el enlace de recuperación al correo del administrador: ' . $admin->email);
        }

        if ($status === Password::RESET_THROTTLED) {
            return back()->withErrors(['email' => 'Debes esperar antes de solicitar otro enlace de recuperación.'])->withInput();
        }

        return back()->withErrors(['email' => __($status)])->withInput();
    }

    /**
     * Enviar enlace de restablecimiento de contraseña al email ingresado
     */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return back()->withErrors(['email' => 'Ese correo no existe o no está creado con nosotros.'])->withInput();
        }
        if (!$user->active) {
            return back()->withErrors(['email' => 'La cuenta está desactivada; por favor contacte al administrador.'])->withInput();
        }

        $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('status', 'Hemos enviado el enlace de recuperación a: ' . $user->email);
        }
        if ($status === Password::RESET_THROTTLED) {
            return back()->withErrors(['email' => 'Debes esperar antes de solicitar otro enlace de recuperación.'])->withInput();
        }

        return back()->withErrors(['email' => __($status)])->withInput();
    }
}
