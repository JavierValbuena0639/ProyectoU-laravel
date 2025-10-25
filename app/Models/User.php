<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Notifications\ResetPassword;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'active',
        'last_login',
        'two_factor_enabled',
        'two_factor_secret',
        'two_factor_confirmed_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'active' => 'boolean',
            'last_login' => 'datetime',
            'two_factor_enabled' => 'boolean',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    /**
     * Eventos del modelo
     */
    protected static function booted(): void
    {
        static::created(function (User $user) {
            // Vincular cualquier usuario creado al administrador (ID=1)
            if ($user->id !== 1) {
                \App\Models\AdminUserAccount::firstOrCreate([
                    'admin_id' => 1,
                    'user_id' => $user->id,
                ]);
            }
        });
    }

    /**
     * Relación con roles
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Relación con transacciones
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Relación con facturas
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Relación con nóminas
     */
    public function payrolls(): HasMany
    {
        return $this->hasMany(Payroll::class);
    }

    /**
     * Relación con impuestos
     */
    public function taxes(): HasMany
    {
        return $this->hasMany(Tax::class);
    }

    /**
     * Relación con auditorías
     */
    public function audits(): HasMany
    {
        return $this->hasMany(Audit::class);
    }

    /**
     * Scope para usuarios activos
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Verificar si es administrador
     */
    public function isAdmin(): bool
    {
        return $this->role && $this->role->isAdmin();
    }

    /**
     * Verificar si es usuario regular
     */
    public function isUser(): bool
    {
        return $this->role && $this->role->isUser();
    }

    /**
     * Verificar si es soporte interno
     */
    public function isSupport(): bool
    {
        return $this->role && method_exists($this->role, 'isSupport') && $this->role->isSupport();
    }

    /**
     * Verificar si es admin o soporte
     */
    public function isAdminOrSupport(): bool
    {
        return $this->isAdmin() || $this->isSupport();
    }

    /**
     * Verificar si es el fundador del servicio (primer usuario del dominio)
     */
    public function isServiceFounder(): bool
    {
        $domain = $this->emailDomain();
        if (!$domain) return false;

        $firstUserId = static::where('email', 'like', '%@' . $domain)
            ->orderBy('id', 'asc')
            ->value('id');

        return $firstUserId && $this->id === (int) $firstUserId;
    }

    /**
     * Obtener el fundador del servicio para el dominio activo
     */
    public static function getServiceFounder(): ?User
    {
        $current = auth()->user();
        $domain = $current ? $current->emailDomain() : null;
        if (!$domain) return null;

        return static::where('email', 'like', '%@' . $domain)
            ->orderBy('id', 'asc')
            ->first();
    }

    /**
     * Obtener el nombre del rol
     */
    public function getRoleName(): string
    {
        return $this->role ? $this->role->display_name : 'Sin rol';
    }

    /**
     * Obtener el dominio del email del usuario (servicio)
     */
    public function emailDomain(): string
    {
        if (!$this->email) return '';
        $parts = explode('@', $this->email);
        return $parts[1] ?? '';
    }

    /**
     * Notificación de restablecimiento de contraseña
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPassword($token));
    }
}
