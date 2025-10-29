<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Store extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'logo_url', // Added from schema
        'address',
        'city',
        'state',
        'zip_code',
        'country',
        'latitude', // Added from schema
        'longitude', // Added from schema
        'phone_number',
        'telegram',
        'email',
        'license_number',
        'opening_time',
        'closing_time',
        'is_24_hours',
        'delivers_product',
        'delivery_details',
        'average_rating', // Added from schema, useful for updates via internal logic
        'review_count', // Added from schema, useful for updates via internal logic
        'is_verified', // Added from schema
        'is_highlighted',
        'is_top_choice',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'latitude' => 'decimal:7', // Cast for precision based on schema
        'longitude' => 'decimal:7', // Cast for precision based on schema
        'opening_time' => 'datetime:H:i:s', // Handles 'time' column type
        'closing_time' => 'datetime:H:i:s', // Handles 'time' column type
        'is_24_hours' => 'boolean',
        'delivers_product' => 'boolean',
        'average_rating' => 'decimal:1', // Cast for precision based on schema
        'is_verified' => 'boolean',
    ];

    /**
     * The attributes that should be mutated to dates.
     * This is handled by the SoftDeletes trait but is kept for clarity.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * Get the columns that should receive a unique identifier.
     *
     * @return array<int, string>
     */
    public function uniqueIds(): array
    {
        return ['id']; // Specify 'id' as the UUID column
    }

	    /**
     * The products that belong to the store.
     */
    // public function products(): BelongsToMany
    // {
    //     return $this->belongsToMany(Product::class, 'store_products')
    //                 ->withPivot('price', 'sale_price', 'quantity', 'is_popular', 'is_on_sale', 'stock_status')
    //                 ->withTimestamps();
    // }

	// public function storeProducts(): HasMany
    // {
    //     return $this->hasMany(StoreProduct::class);
    // }

    public function notificationSettings(): HasMany
    {
        return $this->hasMany(StoreNotificationSetting::class);
    }
}