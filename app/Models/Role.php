<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    protected $fillable = [
        'name',
        'display_name',
        'description',
        'active'
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    /**
     * Relación con usuarios
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Scope para roles activos
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Verificar si es rol de administrador
     */
    public function isAdmin(): bool
    {
        return $this->name === 'admin';
    }

    /**
     * Verificar si es rol de usuario
     */
    public function isUser(): bool
    {
        return $this->name === 'user';
    }

    /**
     * Verificar si es rol de soporte interno
     */
    public function isSupport(): bool
    {
        return strtolower(trim($this->name)) === 'soporte_interno';
    }
}
