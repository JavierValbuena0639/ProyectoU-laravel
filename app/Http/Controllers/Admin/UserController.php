<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerificationCodeMail;
use App\Rules\NotPublicEmailDomain;
use App\Models\Audit;

class UserController extends Controller
{
    /**
     * Mostrar listado de usuarios
     */
    public function index()
    {
        // Filtrar usuarios por dominio del admin autenticado
        $admin = auth()->user();
        $domain = $admin ? $admin->emailDomain() : '';

        // Usuarios del mismo dominio (incluye admin)
        $users = User::with('role')
            ->where(function($q) use ($domain) {
                $q->where('email_domain', $domain);
            })
            ->get();

        // Contadores filtrados
        $totalUsers = $users->count();
        $adminsCount = $users->filter(function ($u) { return $u->role && $u->role->isAdmin(); })->count();
        $activeUsers = $users->filter(function ($u) { return $u->active; })->count();

        return view('admin.users', compact('users', 'totalUsers', 'adminsCount', 'activeUsers'));
    }

    /**
     * Mostrar formulario de creación
     */
    public function create()
    {
        // Ocultar rol de soporte interno para administradores
        $roles = Role::where('name', '!=', 'soporte_interno')->get();
        $expectedDomain = auth()->user()->emailDomain();
        return view('admin.users-create', compact('roles', 'expectedDomain'));
    }

    /**
     * Guardar nuevo usuario
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email', new NotPublicEmailDomain()],
            'password' => ['required', 'string', Password::min(8)->letters()->mixedCase()->numbers()->symbols()->uncompromised(), 'confirmed'],
            'role_id' => ['required', 'integer', 'exists:roles,id'],
            'status' => ['nullable', 'in:active,inactive'],
        ]);

        $active = ($validated['status'] ?? 'active') === 'active';

        // Validar que el dominio del nuevo usuario coincida con el del admin autenticado
        $adminDomain = auth()->user()->emailDomain();
        $newUserDomain = explode('@', $validated['email'])[1] ?? '';
        if ($adminDomain !== $newUserDomain) {
            return back()->withErrors(['email' => 'Solo puedes crear usuarios para el dominio ' . $adminDomain])->withInput();
        }

        // Bloquear asignación del rol de soporte interno por administradores
        $selectedRole = Role::find($validated['role_id']);
        if ($selectedRole && $selectedRole->isSupport()) {
            return back()->withErrors(['role_id' => 'No está permitido asignar el rol de Soporte Interno.'])->withInput();
        }

        // El modelo User tiene cast 'password' => 'hashed', así que no necesitamos hash manual
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role_id' => $validated['role_id'],
            'active' => $active,
        ]);

        // Enviar código verificador por correo al nuevo usuario
        try {
            // OTP aleatorio: 6 dígitos
            $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            Mail::to($user->email)->send(new VerificationCodeMail($user, $code));
            // Guardar código y marca de envío
            $user->forceFill([
                'verification_code' => $code,
                'verification_code_sent_at' => now(),
                'email_verified_at' => null,
            ])->save();
        } catch (\Throwable $e) {
            // Registrar aviso en sesión
            session()->flash('error', 'No fue posible enviar el correo de verificación: ' . $e->getMessage());
        }

        // Vincular usuario creado al administrador actual
        \App\Models\AdminUserAccount::firstOrCreate([
            'admin_id' => auth()->id(),
            'user_id' => $user->id,
        ]);

        return redirect()->route('admin.users')->with('success', 'Usuario creado exitosamente. Se envió un código de verificación por correo.');
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit(User $user)
    {
        // Ocultar rol de soporte interno para administradores
        $roles = Role::where('name', '!=', 'soporte_interno')->get();
        return view('admin.users-edit', compact('user', 'roles'));
    }

    /**
     * Actualizar usuario
     */
    public function update(Request $request, User $user)
    {
        $pw = Password::min(8)->letters()->mixedCase()->numbers()->symbols();
        if (app()->environment('production')) {
            $pw = $pw->uncompromised();
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id, new NotPublicEmailDomain()],
            'password' => ['nullable', 'string', $pw, 'confirmed'],
            'role_id' => ['required', 'integer', 'exists:roles,id'],
            'status' => ['nullable', 'in:active,inactive'],
        ]);

        // Bloquear asignación del rol de soporte interno por administradores
        $selectedRole = Role::find($validated['role_id']);
        if ($selectedRole && $selectedRole->isSupport()) {
            return back()->withErrors(['role_id' => 'No está permitido asignar el rol de Soporte Interno.'])->withInput();
        }

        // Proteger al fundador del servicio (primer usuario creado)
        if ($user->isServiceFounder()) {
            if ((int)$validated['role_id'] !== (int)$user->role_id) {
                return back()->withErrors(['role_id' => 'No puedes cambiar el rol del fundador del servicio.'])->withInput();
            }
            $newStatus = ($validated['status'] ?? ($user->active ? 'active' : 'inactive'));
            if ($newStatus !== 'active') {
                return back()->withErrors(['status' => 'No puedes desactivar la cuenta del fundador del servicio.'])->withInput();
            }
        }

        $oldRoleId = $user->role_id;
        $oldRoleName = optional($user->role)->name;
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->role_id = $validated['role_id'];
        $user->active = ($validated['status'] ?? ($user->active ? 'active' : 'inactive')) === 'active';
        
        if (!empty($validated['password'])) {
            $user->password = $validated['password']; // cast hashed
        }

        $user->save();

        // Auditoría: cambio de rol
        if ((int)$oldRoleId !== (int)$user->role_id) {
            try {
                $newRoleName = optional($user->role)->name;
                Audit::create([
                    'user_id' => auth()->id(), // actor (admin)
                    'event' => 'role_changed',
                    'auditable_type' => 'User',
                    'auditable_id' => $user->id,
                    'old_values' => [
                        'role_id' => $oldRoleId,
                        'role_name' => $oldRoleName,
                    ],
                    'new_values' => [
                        'role_id' => $user->role_id,
                        'role_name' => $newRoleName,
                    ],
                    'ip_address' => $request->ip(),
                    'user_agent' => (string) $request->header('User-Agent'),
                    'url' => $request->fullUrl(),
                    'description' => 'Cambio de rol de ' . ($oldRoleName ?? 'N/A') . ' a ' . ($newRoleName ?? 'N/A') . ' para usuario ID ' . $user->id,
                ]);
            } catch (\Throwable $e) {}
        }

        return redirect()->route('admin.users')->with('success', 'Usuario actualizado exitosamente');
    }

    /**
     * Desactivar (soft-delete) usuario
     */
    public function deactivate(User $user)
    {
        if ($user->isServiceFounder()) {
            return redirect()->route('admin.users')->withErrors(['user' => 'No puedes desactivar la cuenta del fundador del servicio.']);
        }
        $user->active = false;
        $user->save();

        return redirect()->route('admin.users')->with('success', 'Usuario desactivado correctamente');
    }

    /**
     * Reenviar código de verificación por email
     */
    public function resendVerificationCode(User $user)
    {
        // Validar que el admin sólo gestione usuarios de su mismo dominio
        $admin = auth()->user();
        $adminDomain = $admin ? $admin->emailDomain() : '';
        $userDomain = $user ? $user->emailDomain() : '';
        if ($adminDomain !== $userDomain) {
            return redirect()->route('admin.users')->withErrors(['user' => 'Solo puedes gestionar usuarios del dominio ' . $adminDomain]);
        }
    
        // Si ya está verificado, no reenviar
        if (!is_null($user->email_verified_at)) {
            return redirect()->route('admin.users')->with('success', 'Este usuario ya tiene el correo verificado.');
        }
    
        try {
            // OTP aleatorio: 6 dígitos
            $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            Mail::to($user->email)->send(new VerificationCodeMail($user, $code));
            $user->forceFill([
                'verification_code' => $code,
                'verification_code_sent_at' => now(),
            ])->save();
            return redirect()->route('admin.users')->with('success', 'Código de verificación reenviado exitosamente.');
        } catch (\Throwable $e) {
            return redirect()->route('admin.users')->withErrors(['user' => 'No fue posible reenviar el código: ' . $e->getMessage()]);
        }
    }
}