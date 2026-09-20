<?php

namespace App\Observers;

use App\Models\Category;
use Illuminate\Support\Facades\Cache;

class CategoryObserver
{
    /**
     * Clear category-related caches.
     */
    protected function clearCache(Category $category): void
    {
        Cache::forget('all_categories');
        Cache::forget('dashboard_category_count');
    }

    public function saved(Category $category): void
    {
        $this->clearCache($category);
    }

    public function deleted(Category $category): void
    {
        $this->clearCache($category);
    }
}
