<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuoteVersion extends Model
{
    protected $fillable = [
        'quote_id',
        'version',
        'user_id',
        'email_domain',
        'change_reason',
        'snapshot',
    ];

    protected $casts = [
        'snapshot' => 'array',
    ];

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}