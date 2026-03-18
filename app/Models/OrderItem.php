<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'order_id',
        'itemable_id',
        'itemable_type',
        'quantity',
        'unit_price',
        'subtotal'
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function itemable()
    {
        return $this->morphTo();
    }

    public function product()
    {
        return $this->itemable_type === Product::class
            ? $this->belongsTo(Product::class, 'itemable_id')
            : $this->belongsTo(Product::class, 'itemable_id')->whereRaw('1 = 0');
    }
}
