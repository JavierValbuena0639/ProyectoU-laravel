<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    protected $fillable = [
        'business_name',
        'trade_name',
        'document_type',
        'document_number',
        'email',
        'phone',
        'mobile',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'website',
        'contact_person',
        'payment_terms',
        'credit_limit',
        'tax_regime',
        'active',
        'observations'
    ];

    protected $casts = [
        'active' => 'boolean',
        'credit_limit' => 'decimal:2'
    ];

    // Relaciones
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeByDocumentType($query, $type)
    {
        return $query->where('document_type', $type);
    }

    // Métodos auxiliares
    public function getFullNameAttribute(): string
    {
        return $this->trade_name ?: $this->business_name;
    }

    public function getFullAddressAttribute(): string
    {
        $address = $this->address;
        if ($this->city) {
            $address .= ', ' . $this->city;
        }
        if ($this->state) {
            $address .= ', ' . $this->state;
        }
        if ($this->postal_code) {
            $address .= ' ' . $this->postal_code;
        }
        
        return $address;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function getTotalInvoicesAttribute(): int
    {
        return $this->invoices()->count();
    }

    public function getTotalAmountAttribute(): float
    {
        return $this->invoices()->sum('total_amount');
    }
}
