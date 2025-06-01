<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\DataScope;
use App\Traits\DataQuery;

class Permission extends Model
{
    use HasUuids, DataScope, DataQuery;

    protected $fillable = [
        'group',
        'permission_group_id',
        'display_order',
        'module',
        'name',
        'slug',
        'display_order',
        'sub_display_order',
        'developer_only',
        'status',
    ];

	public function roles()
    {
        return $this->belongsToMany(Role::class);
    }
}
