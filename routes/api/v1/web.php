<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\web\ContentBlockController;
use App\Http\Controllers\Api\V1\web\TranslationController;
Route::get('/content-blocks/{id}', [ContentBlockController::class, 'show']);