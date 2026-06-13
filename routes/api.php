<?php

use App\Http\Controllers\Api;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.')->group(function () {
    // Public endpoints
    Route::get('products', [Api\ProductApiController::class, 'index'])->name('products.index');
    Route::get('products/{product:slug}', [Api\ProductApiController::class, 'show'])->name('products.show');
    Route::get('categories', [Api\CategoryApiController::class, 'index'])->name('categories.index');
    Route::get('brands', [Api\BrandApiController::class, 'index'])->name('brands.index');
    Route::post('coupons/validate', [Api\CouponApiController::class, 'validate'])->name('coupons.validate');

    // Auth required
    Route::middleware('auth:sanctum')->group(function () {
        Route::apiResource('cart', Api\CartApiController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::post('orders', [Api\OrderApiController::class, 'store'])->name('orders.store');
        Route::get('orders', [Api\OrderApiController::class, 'index'])->name('orders.index');
        Route::get('orders/{order}', [Api\OrderApiController::class, 'show'])->name('orders.show');
        Route::get('user', fn(\Illuminate\Http\Request $r) => new \App\Http\Resources\UserResource($r->user()->load('roles')))->name('user');
    });

    // Admin API
    Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::apiResource('products', Api\Admin\ProductApiController::class);
        Route::apiResource('categories', Api\Admin\CategoryApiController::class);
        Route::apiResource('brands', Api\Admin\BrandApiController::class);
        Route::apiResource('orders', Api\Admin\OrderApiController::class)->only(['index', 'show', 'update']);
        Route::apiResource('users', Api\Admin\UserApiController::class);
    });
});
