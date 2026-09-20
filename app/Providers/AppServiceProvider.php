<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Stripe\StripeClient;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Observers\ProductObserver;
use App\Observers\CategoryObserver;
use App\Observers\OrderObserver;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(StripeClient::class, function () {
            return new StripeClient(config('services.stripe.secret'));
        });
    }

    public function boot(): void
    {
        Product::observe(ProductObserver::class);
        Category::observe(CategoryObserver::class);
        Order::observe(OrderObserver::class);

        // Admin access gate - for general admin panel
        Gate::define('admin', function ($user) {
            return $user->role === 'admin' || $user->role === 'super_admin';
        });

        // Super admin gate - for sensitive operations
        Gate::define('super-admin', function ($user) {
            return $user->role === 'super_admin';
        });

        // Manage admins gate - for managing admin users (Super Admin only)
        Gate::define('manage-admins', function ($user) {
            return $user->role === 'super_admin';
        });

        // Manage products gate
        Gate::define('manage-products', function ($user) {
            return $user->role === 'admin' || $user->role === 'super_admin';
        });

        // Manage categories gate
        Gate::define('manage-categories', function ($user) {
            return $user->role === 'admin' || $user->role === 'super_admin';
        });

        // View orders gate
        Gate::define('view-orders', function ($user) {
            return $user->role === 'admin' || $user->role === 'super_admin';
        });

        // Edit own profile gate (any authenticated user)
        Gate::define('edit-profile', function ($user) {
            return true;
        });

        // Delete own account gate
        Gate::define('delete-account', function ($user) {
            return true;
        });

        // View own orders gate
        Gate::define('view-own-orders', function ($user) {
            return true;
        });
    }
}
