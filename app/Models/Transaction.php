<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    protected $fillable = [
        'voucher_number',
        'voucher_type',
        'transaction_date',
        'description',
        'account_id',
        'user_id',
        'debit_amount',
        'credit_amount',
        'reference',
        'status'
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'debit_amount' => 'decimal:2',
        'credit_amount' => 'decimal:2',
    ];

    /**
     * Relación con cuenta
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Relación con usuario
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope para transacciones activas
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope para débitos
     */
    public function scopeDebits($query)
    {
        return $query->where('debit_amount', '>', 0);
    }

    /**
     * Scope para créditos
     */
    public function scopeCredits($query)
    {
        return $query->where('credit_amount', '>', 0);
    }

    /**
     * Obtener el monto total de la transacción
     */
    public function getTotalAmountAttribute(): float
    {
        return max($this->debit_amount, $this->credit_amount);
    }

    /**
     * Verificar si es débito
     */
    public function isDebit(): bool
    {
        return $this->debit_amount > 0;
    }

    /**
     * Verificar si es crédito
     */
    public function isCredit(): bool
    {
        return $this->credit_amount > 0;
    }
}
