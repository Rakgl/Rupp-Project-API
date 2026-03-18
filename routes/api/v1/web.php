<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\web\ContentBlockController;
use App\Http\Controllers\Api\V1\web\TranslationController;
use App\Http\Controllers\Api\V1\Web\PayWayCallbackController;

Route::get('/content-blocks/{id}', [ContentBlockController::class, 'show']);

// PayWay payment callback (called by ABA — no auth required)
Route::post('/payway/callback', [PayWayCallbackController::class, 'handle']);