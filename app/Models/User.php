<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

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
}
