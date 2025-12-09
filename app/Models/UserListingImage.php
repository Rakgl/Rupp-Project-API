<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserListingImage extends Model
{
    use HasFactory, HasUuids;
    protected $fillable = ['user_listing_id', 'image_path', 'is_primary'];

    public function listing(): BelongsTo
    {
        return $this->belongsTo(UserListing::class, 'user_listing_id');
    }
}