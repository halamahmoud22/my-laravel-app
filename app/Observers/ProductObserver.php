<?php

namespace App\Observers;

use App\Models\Product;
use Illuminate\Support\Facades\Cache;

class ProductObserver
{
    /**
     * Clear product-related caches.
     */
    protected function clearCache(Product $product): void
    {
        Cache::forget("product_{$product->id}");
        Cache::forget('dashboard_product_count');

        for ($i = 1; $i <= 5; $i++) {
            Cache::forget("home_products_page_{$i}");
        }
        Cache::forget('home_latest_products');
    }

    public function saved(Product $product): void
    {
        $this->clearCache($product);
    }

    public function deleted(Product $product): void
    {
        $this->clearCache($product);
    }
}
