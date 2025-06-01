<?php

namespace App\Models;

use App\Traits\DataScope;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppVersion extends Model
{
    use HasFactory, DataScope, HasUuids;

	protected $table = 'app_versions';
	protected $guarded = ['id'];
	
	protected $fillable = [
		'announcement_id',
		'platform',
		'latest_version',
		'update_url',
		'force_update',
		'message'
	];

	public function announcement()
	{
		return $this->belongsTo(Announcement::class);
	}
}
