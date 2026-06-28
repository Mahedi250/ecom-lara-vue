<?php

use App\Http\Controllers\Admin;
use Illuminate\Support\Facades\Route;

// Admin auth
Route::middleware('guest')->prefix('admin')->name('admin.')->group(function () {
    Route::get('login', [Admin\Auth\AdminLoginController::class, 'create'])->name('login');
    Route::post('login', [Admin\Auth\AdminLoginController::class, 'store'])->name('login.store');
});

Route::post('admin/logout', [Admin\Auth\AdminLoginController::class, 'destroy'])
    ->middleware('auth')
    ->name('admin.logout');

// Admin panel
Route::middleware(['auth', 'role:admin|staff'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');

        Route::resource('products', Admin\ProductController::class);
        Route::patch('products/{product}/toggle-featured', [Admin\ProductController::class, 'toggleFeatured'])->name('products.toggle-featured');
        Route::resource('categories', Admin\CategoryController::class)->except('show');
        Route::resource('brands', Admin\BrandController::class)->except('show');
        Route::get('media', [Admin\MediaController::class, 'index'])->name('media.index');
        Route::post('media', [Admin\MediaController::class, 'store'])->name('media.store');
        Route::delete('media/{medium}', [Admin\MediaController::class, 'destroy'])->name('media.destroy');
        Route::resource('coupons', Admin\CouponController::class)->except('show');

        Route::get('attributes', [Admin\AttributeController::class, 'index'])->name('attributes.index');
        Route::post('attributes', [Admin\AttributeController::class, 'store'])->name('attributes.store');
        Route::put('attributes/{attribute}', [Admin\AttributeController::class, 'update'])->name('attributes.update');
        Route::delete('attributes/{attribute}', [Admin\AttributeController::class, 'destroy'])->name('attributes.destroy');
        Route::post('attributes/{attribute}/values', [Admin\AttributeController::class, 'storeValue'])->name('attributes.values.store');
        Route::put('attributes/{attribute}/values/{value}', [Admin\AttributeController::class, 'updateValue'])->name('attributes.values.update');
        Route::delete('attributes/{attribute}/values/{value}', [Admin\AttributeController::class, 'destroyValue'])->name('attributes.values.destroy');

        Route::get('customers', [Admin\CustomerController::class, 'index'])->name('customers.index');
        Route::post('customers', [Admin\CustomerController::class, 'store'])->name('customers.store');
        Route::patch('customers/{customer}/ban', [Admin\CustomerController::class, 'ban'])->name('customers.ban');
        Route::patch('customers/{customer}/unban', [Admin\CustomerController::class, 'unban'])->name('customers.unban');
        Route::delete('customers/{customer}', [Admin\CustomerController::class, 'destroy'])->name('customers.destroy');

        Route::get('orders', [Admin\OrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{order}', [Admin\OrderController::class, 'show'])->name('orders.show');
        Route::patch('orders/{order}/status', [Admin\OrderController::class, 'updateStatus'])->name('orders.status');

        Route::get('users', [Admin\UserController::class, 'index'])->name('users.index');
        Route::get('users/{user}', [Admin\UserController::class, 'show'])->name('users.show');
        Route::patch('users/{user}', [Admin\UserController::class, 'update'])->name('users.update');
        Route::delete('users/{user}', [Admin\UserController::class, 'destroy'])->name('users.destroy');

        Route::get('settings', [Admin\SettingController::class, 'index'])->name('settings.index');
        Route::post('settings', [Admin\SettingController::class, 'update'])->name('settings.update');

        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/', [Admin\NotificationController::class, 'index'])->name('index');
            Route::post('read-all', [Admin\NotificationController::class, 'markAllRead'])->name('readAll');
            Route::post('{id}/read', [Admin\NotificationController::class, 'markRead'])->name('read');
        });

        Route::prefix('site-setup')->name('site-setup.')->group(function () {
            Route::get('/', [Admin\SiteSetupController::class, 'index'])->name('index');
            Route::post('slides', [Admin\SiteSetupController::class, 'storeSlide'])->name('slides.store');
            Route::post('slides/{slide}', [Admin\SiteSetupController::class, 'updateSlide'])->name('slides.update');
            Route::delete('slides/{slide}', [Admin\SiteSetupController::class, 'destroySlide'])->name('slides.destroy');
            Route::post('assets', [Admin\SiteSetupController::class, 'updateAssets'])->name('assets.update');
            Route::post('reorder', [Admin\SiteSetupController::class, 'reorderSlides'])->name('slides.reorder');
        });
    });
