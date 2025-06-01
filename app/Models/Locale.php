<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\DataQuery;
use App\Traits\DataScope;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Locale extends Model
{
    use HasFactory , DataQuery , DataScope, HasUuids;

    protected $fillable = [
        'name',
        'code',
        'iso',
        'default',
        'status',
    ];
}
