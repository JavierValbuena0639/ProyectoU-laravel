<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tax extends Model
{
    protected $fillable = [
        'user_id',
        'tax_type',
        'tax_period',
        'period_start',
        'period_end',
        'taxable_base',
        'tax_rate',
        'tax_amount',
        'withholding_amount',
        'balance_to_pay',
        'balance_in_favor',
        'due_date',
        'status',
        'observations',
        'details'
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'taxable_base' => 'decimal:2',
        'tax_rate' => 'decimal:4',
        'tax_amount' => 'decimal:2',
        'withholding_amount' => 'decimal:2',
        'balance_to_pay' => 'decimal:2',
        'balance_in_favor' => 'decimal:2',
        'due_date' => 'date',
        'details' => 'array'
    ];

    // Relaciones
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeByType($query, $type)
    {
        return $query->where('tax_type', $type);
    }

    public function scopeByPeriod($query, $start, $end)
    {
        return $query->whereBetween('period_start', [$start, $end]);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())->where('status', '!=', 'paid');
    }

    // Métodos auxiliares
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function isOverdue(): bool
    {
        return $this->due_date < now() && !$this->isPaid();
    }

    public function hasBalanceToPay(): bool
    {
        return $this->balance_to_pay > 0;
    }

    public function hasBalanceInFavor(): bool
    {
        return $this->balance_in_favor > 0;
    }

    public function getNetBalanceAttribute(): float
    {
        return $this->balance_to_pay - $this->balance_in_favor;
    }
}
