<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ContentBlock extends Model
{
    use HasFactory, HasUuids;
    protected $fillable = [
        'title',
        'description',
        'booking_btn',
        'image_path',
        'status',
    ];

    protected $casts = [
        'title' => 'array',
        'description' => 'array',
        'booking_btn' => 'array',
    ];
}