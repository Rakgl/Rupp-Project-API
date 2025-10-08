<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ListingType extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['name', 'description'];

    public function listings()
    {
        return $this->hasMany(Listing::class);
    }
}
