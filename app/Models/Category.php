<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'color',
        'icon',
        'is_default',
        'slug'
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function recurringPayments()
    {
        return $this->hasMany(RecurringPayment::class);
    }

    public function keywords()
    {
        return $this->hasMany(CategoryKeyword::class);
    }
}
