<?php

use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AdminController;

use Illuminate\Support\Facades\Broadcast;

// Register the broadcast auth endpoint for API (Sanctum) clients
// This creates a POST /api/broadcasting/auth route
Broadcast::routes(['middleware' => ['auth:sanctum']]);

// All routes here are prefixed with the 'api.' route name (e.g. api.login, api.cart.index)
Route::name('api.')->group(function () {

    // --- Public endpoints (no token required) ---
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    // Anyone can browse products/categories without logging in
    Route::apiResource('products', ProductsController::class)->only(['index', 'show']);
    Route::apiResource('categories', CategoryController::class)->only(['index', 'show']);

    // --- Protected endpoints (requires a valid Sanctum Bearer token) ---
    Route::middleware('auth:sanctum')->group(function () {

        // Logout requires a token so Sanctum knows *which* token to revoke
        Route::post('/logout', [AuthController::class, 'logout']);

        // --- Products: write actions ---
        // Split into individual routes (instead of apiResource->except) because
        // store/update/destroy each need a DIFFERENT permission - a single
        // apiResource middleware call can't vary permission per HTTP method
        Route::post('products', [ProductsController::class, 'store'])
            ->middleware('permission:products.create')->name('products.store');

        Route::put('products/{product}', [ProductsController::class, 'update'])
            ->middleware('permission:products.update')->name('products.update');
        Route::patch('products/{product}', [ProductsController::class, 'update'])
            ->middleware('permission:products.update'); // same handler as PUT

        Route::delete('products/{product}', [ProductsController::class, 'destroy'])
            ->middleware('permission:products.delete')->name('products.destroy');

        // --- Categories: write actions ---
        // Same reasoning as Products above - split by method so each action
        // checks its own permission
        Route::post('categories', [CategoryController::class, 'store'])
            ->middleware('permission:categories.create')->name('categories.store');

        Route::put('categories/{category}', [CategoryController::class, 'update'])
            ->middleware('permission:categories.update')->name('categories.update');
        Route::patch('categories/{category}', [CategoryController::class, 'update'])
            ->middleware('permission:categories.update');

        Route::delete('categories/{category}', [CategoryController::class, 'destroy'])
            ->middleware('permission:categories.delete')->name('categories.destroy');
        // --- Cart endpoints ---
        // Cart is always scoped to the logged-in user, so no extra permission
        // middleware needed here - ownership is enforced inside the controller
        Route::get('cart', [CartController::class, 'index'])->name('cart.index');
        Route::post('cart', [CartController::class, 'store'])->name('cart.store');
        Route::put('cart/{cart}', [CartController::class, 'update'])->name('cart.update');
        Route::patch('cart/{cart}', [CartController::class, 'update']); // same handler as PUT
        Route::delete('cart/{cart}', [CartController::class, 'destroy'])->name('cart.destroy');
        Route::delete('cart', [CartController::class, 'clear'])->name('cart.clear');

        // --- Orders endpoints ---
        // No 'destroy' - orders are never deleted, only transitioned through
        // their status lifecycle (pending -> paid -> shipped -> completed/cancelled)
        Route::apiResource('orders', OrderController::class)->only(['index', 'show', 'store', 'update']);

        // --- Payment endpoints ---
        // Two-step Stripe-style flow: create a payment intent first, then charge it
        Route::post('/payments/intent', [PaymentController::class, 'createPaymentIntent']);
        Route::post('/payments/charge', [PaymentController::class, 'charge']);

        // --- Notifications endpoints ---
        Route::get('/notifications', [NotificationController::class, 'index']);
        Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount']);
        Route::patch('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
        Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead']);
        Route::delete('/notifications/{id}', [NotificationController::class, 'destroy']);

        // --- Admin management (super_admin only) ---
        Route::post('admins', [AdminController::class, 'store'])
            ->middleware('permission:admins.create')->name('admins.store');

        Route::delete('admins/{admin}', [AdminController::class, 'destroy'])
            ->middleware('permission:admins.delete')->name('admins.destroy');
    }); // <- closes auth:sanctum group
    // route for Stripe webhook to handle payment events (public, no auth)
    Route::post('/webhooks/stripe', [PaymentController::class, 'webhook']);
});
// Simple endpoint to fetch the currently authenticated user from their token
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
