<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payroll extends Model
{
    protected $fillable = [
        'employee_name',
        'employee_document',
        'position',
        'department',
        'user_id',
        'period_start',
        'period_end',
        'basic_salary',
        'overtime_hours',
        'overtime_rate',
        'overtime_amount',
        'bonuses',
        'commissions',
        'allowances',
        'gross_salary',
        'health_contribution',
        'pension_contribution',
        'solidarity_fund',
        'income_tax',
        'other_deductions',
        'total_deductions',
        'net_salary',
        'employer_health',
        'employer_pension',
        'arl',
        'icbf',
        'sena',
        'compensation_fund',
        'total_employer_contributions',
        'status',
        'observations'
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'basic_salary' => 'decimal:2',
        'overtime_hours' => 'decimal:2',
        'overtime_rate' => 'decimal:2',
        'overtime_amount' => 'decimal:2',
        'bonuses' => 'decimal:2',
        'commissions' => 'decimal:2',
        'allowances' => 'decimal:2',
        'gross_salary' => 'decimal:2',
        'health_contribution' => 'decimal:2',
        'pension_contribution' => 'decimal:2',
        'solidarity_fund' => 'decimal:2',
        'income_tax' => 'decimal:2',
        'other_deductions' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'net_salary' => 'decimal:2',
        'employer_health' => 'decimal:2',
        'employer_pension' => 'decimal:2',
        'arl' => 'decimal:2',
        'icbf' => 'decimal:2',
        'sena' => 'decimal:2',
        'compensation_fund' => 'decimal:2',
        'total_employer_contributions' => 'decimal:2'
    ];

    // Relaciones
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeByPeriod($query, $start, $end)
    {
        return $query->whereBetween('period_start', [$start, $end]);
    }

    public function scopeProcessed($query)
    {
        return $query->where('status', 'processed');
    }

    // Métodos auxiliares
    public function isProcessed(): bool
    {
        return $this->status === 'processed';
    }

    public function getTotalCostAttribute(): float
    {
        return $this->net_salary + $this->total_employer_contributions;
    }
}
