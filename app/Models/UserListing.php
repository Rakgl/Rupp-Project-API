<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserListing extends Model
{
    protected $fillable = ['user_id', 'model_id', 'year', 'condition', 'price', 'description', 'status'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function model(): BelongsTo
    {
        return $this->belongsTo(Model::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(UserListingImage::class);
    }
}