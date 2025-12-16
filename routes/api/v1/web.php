<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\web\ContentBlockController;
use App\Http\Controllers\Api\V1\web\TranslationController;
use App\Http\Controllers\Api\V1\web\ServiceCardController;
use App\Http\Controllers\Api\V1\Web\CarController;

Route::get('/translations/{locale}', [TranslationController::class, 'getTranslationsByLocale']);

Route::get('/content-blocks', [ContentBlockController::class, 'index']);
Route::get('/content-blocks/{id}', [ContentBlockController::class, 'show']);

Route::get('/service-cards', [ServiceCardController::class, 'index']);
Route::get('/service-cards/{id}', [ServiceCardController::class, 'show']);

Route::apiResource('cars', CarController::class)->only(['index', 'show']);