<?php
namespace App\Models;

use App\Models\Model as VehicleModel;
use Illuminate\Database\Eloquent\Model as BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserListing extends BaseModel
{
    use HasFactory, HasUuids;
    protected $fillable = ['user_id', 'model_id', 'year', 'condition', 'price', 'description', 'status'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function model(): BelongsTo
    {
        return $this->belongsTo(VehicleModel::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(UserListingImage::class);
    }
}
