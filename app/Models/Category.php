<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'slug',
        'image_url',
        'status'
    ];

    protected $casts = [
        'name' => 'array', // JSON for multi-language
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
