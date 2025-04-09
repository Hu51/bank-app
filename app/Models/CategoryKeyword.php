<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CategoryKeyword extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'keyword',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
