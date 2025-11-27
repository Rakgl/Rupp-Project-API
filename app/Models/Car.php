<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Car extends Model
{
    protected $fillable = [
        'model_id', 'body_type_id', 'stock_quantity', 'status', 'year', 'price', 
        'seat', 'engine', 'door', 'fuel_type', 'condition', 'transmission', 
        'lease_price_per_month'
    ];

    public function model(): BelongsTo
    {
        return $this->belongsTo(Model::class);
    }

    public function bodyType(): BelongsTo
    {
        return $this->belongsTo(BodyType::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(CarImage::class);
    }

    public function leasings(): HasMany
    {
        return $this->hasMany(CarLeasing::class);
    }
}