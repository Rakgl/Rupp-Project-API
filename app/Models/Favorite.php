<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Product;

class Favorite extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'favorable_id',
        'favorable_type',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function favorable()
    {
        return $this->morphTo();
    }
}
