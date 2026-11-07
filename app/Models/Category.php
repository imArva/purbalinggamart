<?php

namespace App\Models;

use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    /** @use HasFactory<CategoryFactory> */
    use HasFactory;

    protected $fillable = [
        'category_name',
        'description',
    ];

    public function getNameAttribute(): string
    {
        return $this->category_name;
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
