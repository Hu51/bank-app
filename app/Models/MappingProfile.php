<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MappingProfile extends Model
{
    protected $fillable = [
        'title',
        'skip_rows',
        'transaction_title',
        'description',
        'counterparty',
        'location',
        'transaction_date',
        'amount',
        'type',
    ];

    protected $casts = [
        'skip_rows' => 'integer',
    ];
}
