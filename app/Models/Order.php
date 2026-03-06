<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'store_id',
        'payment_method_id',
        'order_number',
        'subtotal',
        'tax_amount',
        'delivery_fee',
        'total_amount',
        'fulfillment_type',
        'status',
        'payment_status',
        'delivery_address'
    ];

    protected $casts = [
        'subtotal'      => 'decimal:2',
        'tax_amount'    => 'decimal:2',
        'delivery_fee'  => 'decimal:2',
        'total_amount'  => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
