<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    use AuthorizesRequests;
    public function index()
    {
        $this->authorize('admin');
// Cache::remember checks if the key exists.
// If it does, it returns it. If not, it executes the callback, saves it for 10 minutes, and returns it.
$productCount = Cache::remember('dashboard_product_count', now()->addMinutes(10), function () {
    return Product::count();
});

$categoryCount = Cache::remember('dashboard_category_count', now()->addMinutes(10), function () {
    return Category::count();
});

$orderCount = Cache::remember('dashboard_order_count', now()->addMinutes(10), function () {
    return Order::count();
});

        $recentOrders = Order::with('user')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'productCount',
            'categoryCount',
            'orderCount',
            'recentOrders'
        ));
    }
}
