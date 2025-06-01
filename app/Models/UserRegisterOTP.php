<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\DataQuery;
use App\Traits\DataScope;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class UserRegisterOTP extends Model
{
    use HasFactory, DataQuery, DataScope, HasUuids;

	protected $fillable = [
		'transaction_code',
		'phone',
		'country_code',
		'otp',
		'status',
		'expired_at',
		'attempts',
	];
}
