<?php

use Illuminate\Support\Facades\Route;

Route::prefix('v1/admin')->group(function () {
    require __DIR__.'/api/v1/admin.php';
});

Route::prefix('v1/mobile')->group(function () {
    require __DIR__.'/api/v1/mobile.php';
});

Route::prefix('v1/web')->group(function () {
    require __DIR__.'/api/v1/web.php';
});