<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'type',
        'name',
        'description',
        'slug',
        'image_url',
        'status'
    ];

    protected $casts = [
        'name' => 'json',
        'description' => 'json',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function pets()
    {
        return $this->hasMany(Pet::class);
    }
}
