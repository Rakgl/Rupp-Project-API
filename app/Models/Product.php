<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'attributes',
        'price',
        'sku',
        'image_url',
        'status'
    ];

    protected $casts = [
        'name' => 'array',
        'description' => 'array',
        'attributes' => 'array',
        'price' => 'decimal:2',
        'image_url' => 'array',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function storeInventories()
    {
        return $this->hasMany(StoreInventory::class);
    }

    public function cartItems()
    {
        return $this->morphMany(CartItem::class, 'itemable');
    }

    public function favorites()
    {
        return $this->morphMany(Favorite::class, 'favorable');
    }
}
