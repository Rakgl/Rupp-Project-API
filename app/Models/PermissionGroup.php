<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class PermissionGroup extends Model
{
    use HasUuids;

    protected $fillable = [
        'name',
        'display_order',
    ];

    //Relationship
    public function permissions()
    {
        return $this->hasMany(Permission::class);
    }
}
