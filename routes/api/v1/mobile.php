<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Mobile\AuthController;
use App\Http\Controllers\Api\V1\Mobile\RegisterController;
use App\Http\Controllers\Api\V1\Mobile\ForgetPasswordController;
use App\Http\Controllers\Api\V1\Mobile\TranslationController;
use App\Http\Controllers\Api\V1\Mobile\AppVersionController;
use App\Http\Controllers\Api\V1\Mobile\FcmTokenController;
use App\Http\Controllers\Api\V1\Mobile\NotificationController;
use App\Http\Controllers\Api\V1\Mobile\SettingController;
use App\Http\Controllers\Api\V1\Mobile\ProductController;
use App\Http\Controllers\Api\V1\Mobile\CartController;
use App\Http\Controllers\Api\V1\Mobile\Settings\UserProfileController;
use App\Http\Controllers\Api\V1\Mobile\AppointmentController;
use App\Http\Controllers\Api\V1\Mobile\CategoryController;
use App\Http\Controllers\Api\V1\Mobile\FavoriteController;
use App\Http\Controllers\Api\V1\Mobile\ServiceController;
use App\Http\Controllers\Api\V1\Mobile\PetController;

Route::post('/auth/login', [AuthController::class, 'login']);

// Register
Route::post('/auth/register', [RegisterController::class, 'register']);
Route::post('/auth/verify-phone-number', [RegisterController::class, 'verifyPhoneNumber']);
Route::post('/auth/verify-otp', [RegisterController::class, 'verifyOTP']);
Route::post('/auth/resend-otp', [RegisterController::class, 'resendOTP']);

// Forget Password
Route::post('/forget-password/verify-phone-number', [ForgetPasswordController::class, 'verifyPhoneNumber']);
Route::post('/forget-password/verify-otp', [ForgetPasswordController::class, 'verifyOTP']);
Route::post('/forget-password/reset-password', [ForgetPasswordController::class, 'resetPassword']);
Route::post('/forget-password/resend-otp', [ForgetPasswordController::class, 'resendOTP']);

// Translations & Version
Route::get('/get-translations', [TranslationController::class, 'getTranslations']);
Route::get('/version', [AppVersionController::class, 'version']);
Route::post('/auth/refresh-token', [AuthController::class, 'refreshToken']);

// Auth
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    // User Profile
    Route::get('/user-profile', [UserProfileController::class, 'getUserProfile']);
    Route::post('/user-profile', [UserProfileController::class, 'updateUserProfile']);

    // FCM Token
    Route::post('/fcm-token', [FcmTokenController::class, 'store']);

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/mark-as-read', [NotificationController::class, 'markAsRead']);

    // Settings
    Route::get('/settings', [SettingController::class, 'index']);
    
    // Product
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{product}', [ProductController::class, 'show']);

    // Pets
    Route::get('/pets', [PetController::class, 'index']);
    Route::get('/pets/{pet}', [PetController::class, 'show'])->whereUuid('pet');

    // Categories
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{category}', [CategoryController::class, 'show'])->whereUuid('category');

    // Services
    Route::get('/services', [ServiceController::class, 'index']);
    Route::get('/services/{service}', [ServiceController::class, 'show'])->whereUuid('service');

    // Cart
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart/add', [CartController::class, 'add']);
    Route::put('/cart/items/{cartItem}', [CartController::class, 'update']);
    Route::delete('/cart/items/{cartItem}', [CartController::class, 'remove']);
    Route::delete('/cart/clear', [CartController::class, 'clear']);

    // Appointments
    Route::get('/appointments', [AppointmentController::class, 'index']);
    Route::post('/appointments', [AppointmentController::class, 'store']);
    Route::get('/appointments/{appointment}', [AppointmentController::class, 'show'])->whereUuid('appointment');
    Route::post('/appointments/{appointment}/cancel', [AppointmentController::class, 'cancel'])->whereUuid('appointment');

    // Favorites
    Route::get('/favorites', [FavoriteController::class, 'index']);
    Route::post('/favorites', [FavoriteController::class, 'store']);
    Route::delete('/favorites/{favorite}', [FavoriteController::class, 'destroy'])->whereUuid('favorite');
});
