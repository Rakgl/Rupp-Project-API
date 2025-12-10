<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class AboutUs extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'about_us';

    protected $fillable = [
        'title',
        'description',
        'list_text',
        'image_url',
        'status',
        // 'created_by',
        // 'updated_by',
        // 'deleted_by',
    ];

    protected $casts = [
        'title' => 'array',
        'description' => 'array',
        'list_text' => 'array',
    ];
}