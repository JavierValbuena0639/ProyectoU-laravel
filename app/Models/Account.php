<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'type',
        'nature',
        'parent_id',
        'level',
        'balance',
        'active',
        'accepts_movements'
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'active' => 'boolean',
        'accepts_movements' => 'boolean',
        'level' => 'integer',
    ];

    /**
     * Relación con cuenta padre
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'parent_id');
    }

    /**
     * Relación con cuentas hijas
     */
    public function children(): HasMany
    {
        return $this->hasMany(Account::class, 'parent_id');
    }

    /**
     * Relación con transacciones
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Scope para cuentas activas
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Scope para cuentas que aceptan movimientos
     */
    public function scopeAcceptsMovements($query)
    {
        return $query->where('accepts_movements', true);
    }

    /**
     * Obtener el código completo de la cuenta
     */
    public function getFullCodeAttribute(): string
    {
        return $this->code;
    }

    /**
     * Verificar si es cuenta de activo
     */
    public function isAsset(): bool
    {
        return $this->type === 'activo';
    }

    /**
     * Verificar si es cuenta de pasivo
     */
    public function isLiability(): bool
    {
        return $this->type === 'pasivo';
    }

    /**
     * Verificar si es cuenta de patrimonio
     */
    public function isEquity(): bool
    {
        return $this->type === 'patrimonio';
    }

    /**
     * Verificar si es cuenta de ingreso
     */
    public function isIncome(): bool
    {
        return $this->type === 'ingreso';
    }

    /**
     * Verificar si es cuenta de gasto
     */
    public function isExpense(): bool
    {
        return $this->type === 'gasto';
    }
}
