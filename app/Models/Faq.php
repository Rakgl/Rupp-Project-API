<?php

namespace App\Models;

use App\Traits\DataQuery;
use App\Traits\DataScope;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Faq extends Model implements Auditable
{
    use HasFactory, HasUuids, DataQuery, DataScope , \OwenIt\Auditing\Auditable;

    protected $casts = [
        'answer'=>'array',
        'question'=>'array',
    ];
	protected $fillable = [	
		'question', 
		'answer', 
		'platform', 
		'status',
		'category',
		'image',
		'created_by', 
		'updated_by',
		'update_num'
	];
}
