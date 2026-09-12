<?php

use App\Http\Controllers\Api\ProductsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::name('api.')->group(function () {
    // Public endpoints
    Route::apiResource('products', ProductsController::class)->only(['index', 'show']);

    // Protected endpoints (requires Bearer token)
    Route::middleware('auth:sanctum')->group(function () {
        Route::apiResource('products', ProductsController::class)->except(['index', 'show']);
    });
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
