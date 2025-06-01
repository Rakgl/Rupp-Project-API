<?php

namespace App\Models;

use App\Traits\DataQuery;
use App\Traits\DataScope;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Announcement extends Model implements Auditable
{
    use HasFactory, HasUuids, DataQuery, DataScope, \OwenIt\Auditing\Auditable;

    protected $casts = [
        'title' => 'array',
        'message' => 'array',
    ];

    protected $fillable = [
        'title',
        'message',
        'type',
        'scheduled_at',
        'status',
        'image',
        'sent_at',
        'sent_by',
        'created_by',
        'updated_by',
        'update_num',
    ];

	public function sentBy()
	{
		return $this->belongsTo(User::class, 'sent_by');
	}

	public function appVersions() { 
		return $this->hasMany(AppVersion::class);
	}
}
