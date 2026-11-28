<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Admin\RoleController;
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
Route::middleware(['auth', 'admin.panel'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');

        // ── Catalog: Products ──
        // NOTE: create/store must be registered before show (products/{product})
        // or /admin/products/create would be captured by the show route.
        Route::middleware('role_or_permission:admin|products.create')->group(function () {
            Route::resource('products', Admin\ProductController::class)->only(['create', 'store']);
        });
        Route::middleware('role_or_permission:admin|products.view')->group(function () {
            Route::resource('products', Admin\ProductController::class)->only(['index', 'show']);
        });
        Route::middleware('role_or_permission:admin|products.edit')->group(function () {
            Route::resource('products', Admin\ProductController::class)->only(['edit', 'update']);
            Route::patch('products/{product}/toggle-featured', [Admin\ProductController::class, 'toggleFeatured'])->name('products.toggle-featured');
        });
        Route::middleware('role_or_permission:admin|products.delete')->group(function () {
            Route::resource('products', Admin\ProductController::class)->only(['destroy']);
        });

        // ── Catalog: Categories ──
        Route::middleware('role_or_permission:admin|categories.view')->group(function () {
            Route::resource('categories', Admin\CategoryController::class)->only(['index']);
        });
        Route::middleware('role_or_permission:admin|categories.create')->group(function () {
            Route::resource('categories', Admin\CategoryController::class)->only(['create', 'store']);
        });
        Route::middleware('role_or_permission:admin|categories.edit')->group(function () {
            Route::resource('categories', Admin\CategoryController::class)->only(['edit', 'update']);
        });
        Route::middleware('role_or_permission:admin|categories.delete')->group(function () {
            Route::resource('categories', Admin\CategoryController::class)->only(['destroy']);
        });

        // ── Catalog: Brands ──
        Route::middleware('role_or_permission:admin|brands.view')->group(function () {
            Route::resource('brands', Admin\BrandController::class)->only(['index']);
        });
        Route::middleware('role_or_permission:admin|brands.create')->group(function () {
            Route::resource('brands', Admin\BrandController::class)->only(['create', 'store']);
        });
        Route::middleware('role_or_permission:admin|brands.edit')->group(function () {
            Route::resource('brands', Admin\BrandController::class)->only(['edit', 'update']);
        });
        Route::middleware('role_or_permission:admin|brands.delete')->group(function () {
            Route::resource('brands', Admin\BrandController::class)->only(['destroy']);
        });

        // ── Catalog: Media ──
        Route::middleware('role_or_permission:admin|media.view')->group(function () {
            Route::get('media', [Admin\MediaController::class, 'index'])->name('media.index');
        });
        Route::middleware('role_or_permission:admin|media.create')->group(function () {
            Route::post('media', [Admin\MediaController::class, 'store'])->name('media.store');
        });
        Route::middleware('role_or_permission:admin|media.delete')->group(function () {
            Route::delete('media/{medium}', [Admin\MediaController::class, 'destroy'])->name('media.destroy');
        });

        // ── Catalog: Attributes ──
        Route::middleware('role_or_permission:admin|attributes.view')->group(function () {
            Route::get('attributes', [Admin\AttributeController::class, 'index'])->name('attributes.index');
        });
        Route::middleware('role_or_permission:admin|attributes.create')->group(function () {
            Route::post('attributes', [Admin\AttributeController::class, 'store'])->name('attributes.store');
            Route::post('attributes/{attribute}/values', [Admin\AttributeController::class, 'storeValue'])->name('attributes.values.store');
        });
        Route::middleware('role_or_permission:admin|attributes.edit')->group(function () {
            Route::put('attributes/{attribute}', [Admin\AttributeController::class, 'update'])->name('attributes.update');
            Route::put('attributes/{attribute}/values/{value}', [Admin\AttributeController::class, 'updateValue'])->name('attributes.values.update');
        });
        Route::middleware('role_or_permission:admin|attributes.delete')->group(function () {
            Route::delete('attributes/{attribute}', [Admin\AttributeController::class, 'destroy'])->name('attributes.destroy');
            Route::delete('attributes/{attribute}/values/{value}', [Admin\AttributeController::class, 'destroyValue'])->name('attributes.values.destroy');
        });

        // ── Catalog: Customers ──
        Route::middleware('role_or_permission:admin|customers.view')->group(function () {
            Route::get('customers', [Admin\CustomerController::class, 'index'])->name('customers.index');
        });
        Route::middleware('role_or_permission:admin|customers.create')->group(function () {
            Route::post('customers', [Admin\CustomerController::class, 'store'])->name('customers.store');
        });
        Route::middleware('role_or_permission:admin|customers.edit')->group(function () {
            Route::patch('customers/{customer}/ban', [Admin\CustomerController::class, 'ban'])->name('customers.ban');
            Route::patch('customers/{customer}/unban', [Admin\CustomerController::class, 'unban'])->name('customers.unban');
        });
        Route::middleware('role_or_permission:admin|customers.delete')->group(function () {
            Route::delete('customers/{customer}', [Admin\CustomerController::class, 'destroy'])->name('customers.destroy');
        });

        // ── Sales: Orders ──
        Route::middleware('role_or_permission:admin|orders.view')->group(function () {
            Route::get('orders', [Admin\OrderController::class, 'index'])->name('orders.index');
            Route::get('orders/{order}', [Admin\OrderController::class, 'show'])->name('orders.show');
        });
        Route::middleware('role_or_permission:admin|orders.edit')->group(function () {
            Route::patch('orders/{order}/status', [Admin\OrderController::class, 'updateStatus'])->name('orders.status');
        });

        // ── Sales: Coupons ──
        Route::middleware('role_or_permission:admin|coupons.view')->group(function () {
            Route::resource('coupons', Admin\CouponController::class)->only(['index']);
        });
        Route::middleware('role_or_permission:admin|coupons.create')->group(function () {
            Route::resource('coupons', Admin\CouponController::class)->only(['create', 'store']);
        });
        Route::middleware('role_or_permission:admin|coupons.edit')->group(function () {
            Route::resource('coupons', Admin\CouponController::class)->only(['edit', 'update']);
        });
        Route::middleware('role_or_permission:admin|coupons.delete')->group(function () {
            Route::resource('coupons', Admin\CouponController::class)->only(['destroy']);
        });

        // ── People: Users ──
        // /users/create must be registered before /users/{user}
        Route::middleware('role_or_permission:admin|users.create')->group(function () {
            Route::get('users/create', [Admin\UserController::class, 'create'])->name('users.create');
            Route::post('users', [Admin\UserController::class, 'store'])->name('users.store');
        });
        Route::middleware('role_or_permission:admin|users.view')->group(function () {
            Route::get('users', [Admin\UserController::class, 'index'])->name('users.index');
            Route::get('users/{user}', [Admin\UserController::class, 'show'])->name('users.show');
        });
        Route::middleware('role_or_permission:admin|users.edit')->group(function () {
            Route::patch('users/{user}', [Admin\UserController::class, 'update'])->name('users.update');
        });
        Route::middleware('role_or_permission:admin|users.credentials')->group(function () {
            Route::patch('users/{user}/password', [Admin\UserController::class, 'updatePassword'])->name('users.password');
        });
        Route::middleware('role_or_permission:admin|users.delete')->group(function () {
            Route::delete('users/{user}', [Admin\UserController::class, 'destroy'])->name('users.destroy');
        });

        // ── People: Roles (super admin only) ──
        Route::middleware('role:admin')->resource('roles', RoleController::class)->except('show');

        // ── System: Settings ──
        Route::middleware('role_or_permission:admin|settings.view')->group(function () {
            Route::get('settings', [Admin\SettingController::class, 'index'])->name('settings.index');
        });
        Route::middleware('role_or_permission:admin|settings.edit')->group(function () {
            Route::post('settings', [Admin\SettingController::class, 'update'])->name('settings.update');
        });

        // ── System: Site Setup ──
        Route::middleware('role_or_permission:admin|site-setup.view')->group(function () {
            Route::get('site-setup', [Admin\SiteSetupController::class, 'index'])->name('site-setup.index');
        });
        Route::middleware('role_or_permission:admin|site-setup.edit')->group(function () {
            Route::prefix('site-setup')->name('site-setup.')->group(function () {
                Route::post('slides', [Admin\SiteSetupController::class, 'storeSlide'])->name('slides.store');
                Route::post('slides/{slide}', [Admin\SiteSetupController::class, 'updateSlide'])->name('slides.update');
                Route::delete('slides/{slide}', [Admin\SiteSetupController::class, 'destroySlide'])->name('slides.destroy');
                Route::post('assets', [Admin\SiteSetupController::class, 'updateAssets'])->name('assets.update');
                Route::post('reorder', [Admin\SiteSetupController::class, 'reorderSlides'])->name('slides.reorder');
            });
        });

        // ── System: Pages (CMS) ──
        Route::middleware('role_or_permission:admin|pages.view')->group(function () {
            Route::resource('pages', Admin\PageController::class)->only(['index']);
        });
        Route::middleware('role_or_permission:admin|pages.create')->group(function () {
            Route::resource('pages', Admin\PageController::class)->only(['create', 'store']);
        });
        Route::middleware('role_or_permission:admin|pages.edit')->group(function () {
            Route::resource('pages', Admin\PageController::class)->only(['edit', 'update']);
        });
        Route::middleware('role_or_permission:admin|pages.delete')->group(function () {
            Route::resource('pages', Admin\PageController::class)->only(['destroy']);
        });

        // ── Notifications (panel-wide) ──
        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/', [Admin\NotificationController::class, 'index'])->name('index');
            Route::post('read-all', [Admin\NotificationController::class, 'markAllRead'])->name('readAll');
            Route::post('{id}/read', [Admin\NotificationController::class, 'markRead'])->name('read');
        });
    });
