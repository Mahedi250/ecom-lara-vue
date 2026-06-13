<?php

namespace App\Providers;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Policies\OrderPolicy;
use App\Policies\ProductPolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(\App\Services\ProductService::class);
        $this->app->bind(\App\Services\CartService::class);
        $this->app->bind(\App\Services\OrderService::class);
        $this->app->bind(\App\Services\CouponService::class);
        $this->app->bind(\App\Services\MediaService::class);
    }

    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        // Policies
        Gate::policy(Product::class, ProductPolicy::class);
        Gate::policy(Order::class, OrderPolicy::class);
        Gate::policy(User::class, UserPolicy::class);

        // Events

        // Admin gate
        Gate::define('admin', fn(User $user) => $user->hasRole('admin'));
        Gate::define('staff', fn(User $user) => $user->hasRole(['admin', 'staff']));
    }
}
