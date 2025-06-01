<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLogin extends Model
{
    use HasFactory, HasUuids;

	protected $fillable = [
		'type',
		'user_id', 
		'ip_address',
		'browser',
		'login_at', 
		'logout_at'
	];
}
