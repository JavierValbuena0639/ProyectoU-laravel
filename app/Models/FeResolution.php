<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeResolution extends Model
{
    protected $fillable = [
        'prefix',
        'number_from',
        'number_to',
        'start_date',
        'end_date',
        'active',
    ];

    protected $casts = [
        'number_from' => 'integer',
        'number_to' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
        'active' => 'boolean',
    ];
}