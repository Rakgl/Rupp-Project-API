<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\V1\Admin\Security\AuthController;
use App\Http\Controllers\Api\V1\Admin\Security\RegisterController;
use App\Http\Controllers\Api\V1\Admin\Security\UserController;
use App\Http\Controllers\Api\V1\Admin\Security\RoleController;
use App\Http\Controllers\Api\V1\Admin\Security\RolePermissionController;
use App\Http\Controllers\Api\V1\Admin\Configuration\SettingController;
use App\Http\Controllers\Api\V1\Admin\TranslationController;
use App\Http\Controllers\Api\V1\Admin\GeneralSettingController;
use App\Http\Controllers\Api\V1\Admin\StoreController;
use App\Http\Controllers\Api\V1\Admin\PaymentMethodController;
use App\Http\Controllers\Api\V1\Admin\StoreNotificationSettingController;
use App\Http\Controllers\Api\V1\Admin\AppDownloadLinkController;
use App\Http\Controllers\Api\V1\Admin\AppVersionController;
use App\Http\Controllers\Api\V1\Admin\DashboardController;
use App\Http\Controllers\Api\V1\Admin\CategoryController;
use App\Http\Controllers\Api\V1\Admin\ProductController;
use App\Http\Controllers\Api\V1\Admin\StoreInventoryController;
use App\Http\Controllers\Api\V1\Admin\OrderController;

// Public routes
Route::post('/auth/login', [AuthController::class, 'login']);
Route::get('/settings', [SettingController::class, 'index']);
Route::post('/auth/refresh-token', [AuthController::class, 'refreshToken']);
Route::get('/debug-ip', function (Request $request) {
    return response()->json([
        'laravel_request_ip' => $request->ip(),
        'server_remote_addr' => $_SERVER['REMOTE_ADDR'] ?? 'not set',
        'header_x_forwarded_for' => $request->header('X-Forwarded-For'),
        'all_headers' => $request->headers->all(),
    ]);
});


Route::get('/translations/{locale}', [TranslationController::class, 'getTranslationsByLocale']);
// Authenticated routes
Route::middleware(['auth:sanctum'])->group(function () {
	Route::post('/auth/logout', [AuthController::class, 'logout']);

    // Security
    Route::get('/auth/info', [AuthController::class, 'info']);

    // Admin-only routes
    Route::middleware(['ability:admin'])->group(function () {

        // Auth
        Route::post('/auth/register', [RegisterController::class, 'register']);

		Route::get('/auth/get-user', [AuthController::class, 'getUser']);

        // Users
        Route::get('/users/edit/{id}', [UserController::class, 'edit']);
        Route::get('/users/audit/{id}', [UserController::class, 'audit']);
        Route::post('/users/upload-profile/{userId}', [UserController::class, 'uploadProfile']);
        Route::delete('/users/remove-profile/{userId}', [UserController::class, 'removeProfile']);
        Route::put('/users/change-password/{userId}', [UserController::class, 'changePassword']);
        Route::post('/users/update-user/{id}', [UserController::class, 'update']);
        Route::resource('/users', UserController::class);

		Route::post('/users/update-profile/{userId}', [UserController::class, 'updateProfile']);

        // Roles
        Route::get('roles/audit/{id}', [RoleController::class, 'audit']);
        Route::get('roles/active', [RoleController::class, 'active']);
        Route::resource('/roles', RoleController::class);
        Route::post('roles/suggest', [RoleController::class, 'suggestRoles']);

        // Role Permissions
        Route::get('/role-permissions/role/{id}', [RolePermissionController::class, 'permissionsByRole']);
        Route::get('/role-permissions', [RolePermissionController::class, 'index']);
        Route::post('/role-permissions/update', [RolePermissionController::class, 'updateRolePermission']);

        // General setting
        Route::get('general-settings', [GeneralSettingController::class, 'index']);
        Route::post('general-settings', [GeneralSettingController::class, 'storeOrUpdate']);

        // Mobile Setting Updates
        Route::post('settings', [SettingController::class, 'update']);

        Route::apiResource('translations',TranslationController::class);

        Route::apiResource('/stores/fetch', StoreController::class);
        Route::apiResource('/stores', StoreController::class);
        Route::apiResource('stores.notification-settings', StoreNotificationSettingController::class)->shallow();
        Route::post('notification-settings/{notification_setting}/test', [StoreNotificationSettingController::class, 'test'])
            ->name('notification-settings.test');
        Route::post('notification-settings/get-chat-id', [StoreNotificationSettingController::class, 'getChatId'])
            ->name('notification-settings.get-chat-id');

        // Payment Methods
        Route::apiResource('payment-methods', PaymentMethodController::class);

        // App Download Links
        Route::get('app-download-links', [AppDownloadLinkController::class, 'index']);
        Route::put('app-download-links/{id}', [AppDownloadLinkController::class, 'update']);

        // App Versions
        Route::get('app-versions', [AppVersionController::class, 'index']);
	    Route::post('app-versions', [AppVersionController::class, 'updateConfig']);

        // Main Dashboard Overview
        Route::get('dashboard', [DashboardController::class, 'index']);

        // Category
        Route::get('categories', [CategoryController::class, 'index']);
        Route::get('categories/{id}', [CategoryController::class, 'show']);
	    Route::post('categories', [CategoryController::class, 'store']);
	    Route::put('categories/{id}', [CategoryController::class, 'update']);
	    Route::delete('categories/{id}', [CategoryController::class, 'destroy']);

        // Product
        Route::get('products', [ProductController::class, 'index']);
        Route::get('products/{id}', [ProductController::class, 'show']);
        Route::post('products', [ProductController::class, 'store']);
        Route::put('products/{id}', [ProductController::class, 'update']);
        Route::delete('products/{id}', [ProductController::class, 'destroy']);

        // Store Inventory
        Route::get('store-inventory', [StoreInventoryController::class, 'index']);
        Route::get('store-inventory/{id}', [StoreInventoryController::class, 'show']);
        Route::post('store-inventory', [StoreInventoryController::class, 'store']);
        Route::put('store-inventory/{id}', [StoreInventoryController::class, 'update']);
        Route::delete('store-inventory/{id}', [StoreInventoryController::class, 'destroy']);

        // Orders
        Route::apiResource('orders', OrderController::class)->except(['store', 'destroy']);
        Route::put('orders/{id}/status', [OrderController::class, 'updateStatus']);
        Route::get('orders/{id}/items', [OrderController::class, 'getItems']);
    });
});