<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Brand extends Model
{
    use HasFactory, HasUuids;
    protected $fillable = ['name', 'image_url'];

    public function models(): HasMany
    {
        return $this->hasMany(\App\Models\Model::class);
    }
}