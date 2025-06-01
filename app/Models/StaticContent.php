<?php

namespace App\Models;

use App\Traits\DataQuery;
use App\Traits\DataScope;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class StaticContent extends Model implements Auditable
{
    use HasFactory, HasUuids, DataQuery, DataScope, \OwenIt\Auditing\Auditable;

    protected $auditInclude = [
        'title',
        'content',
        'type',
        'status',
    ];

    protected $fillable = [
        'title',
        'image',
        'content',
        'type',
        'status',
        'created_by',
        'updated_by',
        'update_num',
    ];

    // 👉 Add this:
    protected $casts = [
        'title' => 'array',
        'content' => 'array',
    ];
}

