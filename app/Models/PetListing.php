<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PetListing extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'store_id',
        'pet_id',
        'listing_type',
        'price',
        'description',
        'status'
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }
}
