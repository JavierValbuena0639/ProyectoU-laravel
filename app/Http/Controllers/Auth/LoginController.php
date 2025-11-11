<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Password;
use Illuminate\Cache\RateLimiter;
use Illuminate\Support\Str;
use App\Models\Audit;

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

        // Política de bloqueo tras intentos fallidos
        /** @var RateLimiter $limiter */
        $limiter = app(RateLimiter::class);
        $key = 'login:'.Str::lower($email).'|'.$request->ip();
        $maxAttempts = 5;       // máximo de intentos permitidos
        $decaySeconds = 15 * 60; // ventana/bloqueo de 15 minutos

        if ($limiter->tooManyAttempts($key, $maxAttempts)) {
            $seconds = $limiter->availableIn($key);
            try {
                Audit::create([
                    'user_id' => optional($user ?? null)->id,
                    'event' => 'login_blocked',
                    'auditable_type' => 'User',
                    'auditable_id' => optional($user ?? null)->id,
                    'ip_address' => $request->ip(),
                    'user_agent' => (string) $request->header('User-Agent'),
                    'url' => $request->fullUrl(),
                    'description' => 'Intento de inicio de sesión bloqueado por rate limit',
                ]);
            } catch (\Throwable $e) {}
            throw ValidationException::withMessages([
                'email' => ['Demasiados intentos fallidos. Intenta nuevamente en ' . $seconds . ' segundos.'],
            ]);
        }

        $user = User::where('email', $email)->first();
        if (!$user || !Hash::check($password, $user->password)) {
            // Registrar intento fallido y aplicar bloqueo si corresponde
            $limiter->hit($key, $decaySeconds);
            try {
                Audit::create([
                    'user_id' => optional($user)->id,
                    'event' => 'login_failed',
                    'auditable_type' => 'User',
                    'auditable_id' => optional($user)->id,
                    'ip_address' => $request->ip(),
                    'user_agent' => (string) $request->header('User-Agent'),
                    'url' => $request->fullUrl(),
                    'description' => 'Credenciales inválidas para email: ' . Str::lower($email),
                ]);
            } catch (\Throwable $e) {}
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
        // Limpiar contador de intentos al autenticar correctamente
        $limiter->clear($key);

        Auth::login($user, $remember);
        $request->session()->regenerate();

        // Actualizar último login
        $user->update([
            'last_login' => now()
        ]);

        // Auditoría: login exitoso
        try {
            Audit::create([
                'user_id' => $user->id,
                'event' => 'login_success',
                'auditable_type' => 'User',
                'auditable_id' => $user->id,
                'ip_address' => $request->ip(),
                'user_agent' => (string) $request->header('User-Agent'),
                'url' => $request->fullUrl(),
                'description' => 'Inicio de sesión exitoso',
            ]);
        } catch (\Throwable $e) {}

        // Redirigir según el rol del usuario (sin usar intended para evitar desvíos)
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        if (method_exists($user, 'isSupport') && $user->isSupport()) {
            return redirect()->route('admin.database');
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
