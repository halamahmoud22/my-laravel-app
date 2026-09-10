<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class DashboardController extends Controller
{
    use AuthorizesRequests;
    public function index()
    {
        $this->authorize('admin');

        $productCount = Product::count();
        $categoryCount = Category::count();
        $orderCount = Order::count();

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
