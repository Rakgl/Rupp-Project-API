<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreInventory extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'store_inventory';

    protected $fillable = [
        'store_id',
        'product_id',
        'stock_quantity'
    ];

    protected $appends = ['status'];

    public function getStatusAttribute()
    {
        if ($this->stock_quantity <= 0) {
            return 'OUT_OF_STOCK';
        } elseif ($this->stock_quantity < 20) {
            return 'LOW_STOCK';
        }

        return 'IN_STOCK';
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
