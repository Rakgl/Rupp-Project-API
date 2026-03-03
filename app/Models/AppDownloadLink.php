<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppDownloadLink extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'app_download_links';

    protected $fillable = [
        'platform',
        'name',
        'url',
        'qr_code_svg',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
