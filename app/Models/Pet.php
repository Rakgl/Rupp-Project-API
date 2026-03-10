<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pet extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'store_id',
        'name',
        'species',
        'breed',
        'weight',
        'date_of_birth',
        'image_url',
        'medical_notes'
    ];

    protected $casts = [
        'weight' => 'decimal:2',
        'date_of_birth' => 'date',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function listings()
    {
        return $this->hasMany(PetListing::class);
    }
}
