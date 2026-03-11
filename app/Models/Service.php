<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'description',
        'price',
        'duration_minutes',
        'image_url',
        'status'
    ];

    protected $casts = [
        'name' => 'array',
        'description' => 'array',
        'price' => 'decimal:2',
    ];

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function cartItems()
    {
        return $this->morphMany(CartItem::class, 'itemable');
    }

    public function favorites()
    {
        return $this->morphMany(Favorite::class, 'favorable');
    }
}
