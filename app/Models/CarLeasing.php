<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CarLeasing extends Model
{
    protected $fillable = ['car_id', 'user_id', 'start_date', 'end_date', 'monthly_price', 'total_price', 'status'];

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}