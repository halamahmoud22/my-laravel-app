<?php

use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::name('api.')->group(function () {
    // Public endpoints
    Route::apiResource('products', ProductsController::class)->only(['index', 'show']);
    Route::apiResource('categories', CategoryController::class)->only(['index', 'show']);

    // Protected endpoints (requires Bearer token)
    Route::middleware('auth:sanctum')->group(function () {
        Route::apiResource('products', ProductsController::class)->except(['index', 'show']);
        Route::apiResource('categories', CategoryController::class)->except(['index', 'show']);

        // Cart endpoints
        Route::get('cart', [CartController::class, 'index'])->name('cart.index');
        Route::post('cart', [CartController::class, 'store'])->name('cart.store');
        Route::post('cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
        Route::put('cart/{cart}', [CartController::class, 'update'])->name('cart.update');
        Route::patch('cart/{cart}', [CartController::class, 'update']);
        Route::delete('cart/{cart}', [CartController::class, 'destroy'])->name('cart.destroy');
        Route::delete('cart', [CartController::class, 'clear'])->name('cart.clear');

        // Orders endpoints
        Route::apiResource('orders', OrderController::class)->only(['index', 'show', 'store', 'update']);
    });
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
