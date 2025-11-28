<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'amount',
        'type',
        'transaction_title',
        'description',
        'counterparty',
        'card_number',
        'transaction_date',
        'source',
        'reference_id',
        'metadata',
        'comment',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'transaction_date' => 'date',
        'metadata' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function smsMessage()
    {
        return $this->hasOne(SmsMessage::class);
    }


    public function categorize()
    {
        $categories = Category::with('keywords')->get();
        $categories = $categories->sortBy(function($category) {
            return strlen($category->keyword);
        });
            
        foreach ($categories as $cat) {
            foreach ($cat->keywords as $keyword) {
                if (
                    str_contains(mb_strtoupper($this->description), mb_strtoupper($keyword->keyword)) || 
                    str_contains(mb_strtoupper($this->transaction_title), mb_strtoupper($keyword->keyword)) || 
                    str_contains(mb_strtoupper($this->counterparty), mb_strtoupper($keyword->keyword))
                ) {
                    $this->category_id = $cat->id;
                    break 2;
                }
            }
        }

        $this->save();
    }


}
