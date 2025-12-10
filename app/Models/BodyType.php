<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BodyType extends Model
{
    use HasFactory, HasUuids;
    protected $fillable = ['name', 'image_url'];

    public function cars(): HasMany
    {
        return $this->hasMany(Car::class);
    }
}