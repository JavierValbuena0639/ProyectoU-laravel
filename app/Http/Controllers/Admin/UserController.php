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
                $q->where('email', 'like', '%@' . $domain);
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
            $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            Mail::to($user->email)->send(new VerificationCodeMail($user, $code));
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
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id, new NotPublicEmailDomain()],
            'password' => ['nullable', 'string', Password::min(8)->letters()->mixedCase()->numbers()->symbols()->uncompromised(), 'confirmed'],
            'role_id' => ['required', 'integer', 'exists:roles,id'],
            'status' => ['nullable', 'in:active,inactive'],
        ]);

        // Bloquear asignación del rol de soporte interno por administradores
        $selectedRole = Role::find($validated['role_id']);
        if ($selectedRole && $selectedRole->isSupport()) {
            return back()->withErrors(['role_id' => 'No está permitido asignar el rol de Soporte Interno.'])->withInput();
        }

        // No permitir cambiar rol ni desactivar al Administrador
        if ($user->isAdmin()) {
            if ((int)$validated['role_id'] !== (int)$user->role_id) {
                return back()->withErrors(['role_id' => 'No puedes cambiar el rol del usuario Administrador.'])->withInput();
            }
            $newStatus = ($validated['status'] ?? ($user->active ? 'active' : 'inactive'));
            if ($newStatus !== 'active') {
                return back()->withErrors(['status' => 'No puedes desactivar la cuenta del usuario Administrador.'])->withInput();
            }
        }

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->role_id = $validated['role_id'];
        $user->active = ($validated['status'] ?? ($user->active ? 'active' : 'inactive')) === 'active';
        
        if (!empty($validated['password'])) {
            $user->password = $validated['password']; // cast hashed
        }

        $user->save();

        return redirect()->route('admin.users')->with('success', 'Usuario actualizado exitosamente');
    }

    /**
     * Desactivar (soft-delete) usuario
     */
    public function deactivate(User $user)
    {
        if ($user->isAdmin()) {
            return redirect()->route('admin.users')->withErrors(['user' => 'No puedes desactivar la cuenta del usuario Administrador.']);
        }
        $user->active = false;
        $user->save();

        return redirect()->route('admin.users')->with('success', 'Usuario desactivado correctamente');
    }
}