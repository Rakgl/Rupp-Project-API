<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model as BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Model as VehicleModel;

class Review extends BaseModel
{
    use HasFactory, HasUuids;

    protected $fillable = ['model_id', 'user_id', 'rating', 'comment'];

    public function model(): BelongsTo
    {
        return $this->belongsTo(VehicleModel::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}