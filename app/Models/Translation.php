<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\DataQuery;
use App\Traits\DataScope;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use OwenIt\Auditing\Contracts\Auditable;

class Translation extends Model implements Auditable
{
    use HasFactory , DataQuery , DataScope, HasUuids , \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'key',
        'value',
        'platform',
        'status',
	];
}
