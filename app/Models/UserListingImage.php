<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserListingImage extends Model
{
    protected $fillable = ['user_listing_id', 'image_path', 'is_primary'];

    public function listing(): BelongsTo
    {
        return $this->belongsTo(UserListing::class, 'user_listing_id');
    }
}