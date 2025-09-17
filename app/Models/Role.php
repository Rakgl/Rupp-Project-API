<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\DataScope;
use OwenIt\Auditing\Contracts\Auditable;
class Role extends Model implements Auditable
{
    use HasFactory, HasUuids, DataScope , \OwenIt\Auditing\Auditable;

	protected $auditInclude = [
        'name',
        'description',
		'status',
        'type'
    ];

    protected $fillable=[
        'name',
        'description',
        'status',
        'type',
		'created_by', 
		'updated_by',
		'update_num'
    ];

    // Local Scope
    public function scopeExceptRoot($q) {
		return $q->where('name', '!=', 'Super Admin')
                 ->where('name', '!=', 'Developer');
	}

	public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permissions');
    }
}