<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CarSale extends Model
{
    use HasFactory, HasUuids;
    protected $fillable = ['car_id', 'buyer_id', 'final_price', 'status'];

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }
    
    /**
     * Get the payments associated with this car sale. (Polymorphic Relation)
     */
    public function payments(): MorphMany
    {
        return $this->morphMany(Payment::class, 'payable');
    }
}