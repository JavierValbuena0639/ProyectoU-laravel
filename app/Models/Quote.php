<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quote extends Model
{
    protected $fillable = [
        'quote_number',
        'user_id',
        'email_domain',
        'client_name',
        'client_document',
        'client_email',
        'client_company',
        'client_address',
        'issue_date',
        'valid_until',
        'project_description',
        'items',
        'subtotal',
        'tax_amount',
        'total_amount',
        'status',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'valid_until' => 'date',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'items' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function versions(): HasMany
    {
        return $this->hasMany(QuoteVersion::class);
    }
}