<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Payment extends Model
{
    use HasFactory, HasUuids;
    protected $fillable = ['user_id', 'payable_type', 'payable_id', 'amount', 'method', 'transaction_id', 'status'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the parent model (CarSale, CarLeasing, etc.) that the payment belongs to.
     */
    public function payable(): MorphTo
    {
        return $this->morphTo();
    }
}