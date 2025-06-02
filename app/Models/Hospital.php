<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\DataQuery;
use App\Traits\DataScope;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Hospital extends Model
{
	use HasFactory, HasUuids, DataQuery, DataScope;

	protected $fillable = [
		'name',
		'address',
		'city',
		'state',
		'zip_code',
		'country',
		'phone_number',
		'email',
		'website',
		'description',
		'status',
		'created_by',
		'updated_by',
		'deleted_by',
	];
}
