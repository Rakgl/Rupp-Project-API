<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BodyType extends Model
{
    protected $fillable = ['name', 'image_url'];

    public function cars(): HasMany
    {
        return $this->hasMany(Car::class);
    }
}