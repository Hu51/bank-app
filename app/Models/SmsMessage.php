<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SmsMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'message',
        'sender',
        'received_at',
        'is_processed',
        'transaction_id',
        'parsed_data',
    ];

    protected $casts = [
        'received_at' => 'datetime',
        'is_processed' => 'boolean',
        'parsed_data' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
