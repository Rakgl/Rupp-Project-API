<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model as VehicleModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Model extends VehicleModel
{
    use HasFactory, HasUuids;
    protected $fillable = ['brand_id', 'name'];

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function cars(): HasMany
    {
        return $this->hasMany(Car::class);
    }

    public function userListings(): HasMany
    {
        return $this->hasMany(UserListing::class);
    }
    
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }
}