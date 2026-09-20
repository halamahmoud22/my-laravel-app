<?php

namespace App\Observers;

use App\Models\Order;
use Illuminate\Support\Facades\Cache;

class OrderObserver
{
    /**
     * Clear order-related caches.
     */
    protected function clearCache(Order $order): void
    {
        Cache::forget('dashboard_order_count');
        Cache::forget('dashboard_recent_orders');
    }

    public function saved(Order $order): void
    {
        $this->clearCache($order);
    }

    public function deleted(Order $order): void
    {
        $this->clearCache($order);
    }
}
