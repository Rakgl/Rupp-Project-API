<?php

namespace App\Models;

use App\Traits\DataScope;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AppVersion extends Model
{
    use HasFactory, DataScope, HasUuids, SoftDeletes;

	protected $table = 'app_versions';
	protected $guarded = ['id'];
	
	protected $fillable = [
        'app',
		'announcement_id',
		'platform',
		'latest_version',
        'min_supported_version',
		'update_url',
		'force_update',
        'title',
		'message',
        'created_by',
        'updated_by',
        'deleted_by',
	];

	public function announcement()
	{
		return $this->belongsTo(Announcement::class);
	}
}
