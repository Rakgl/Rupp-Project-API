<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pet extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'category_id',
        'name',
        'species',
        'breed',
        'weight',
        'date_of_birth',
        'image_url',
        'medical_notes',
        'price'
    ];

    protected $casts = [
        'weight' => 'decimal:2',
        'date_of_birth' => 'date',
        'price' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function listings()
    {
        return $this->hasMany(PetListing::class);
    }

    public function favorites()
    {
        return $this->morphMany(Favorite::class, 'favorable');
    }
}
