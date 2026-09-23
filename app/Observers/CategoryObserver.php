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
        Cache::forget('api_all_categories');
        Cache::forget("api_category_{$category->id}");
        Cache::forget('dashboard_category_count');

        for ($i = 1; $i <= 10; $i++) {
            Cache::forget("api_categories_page_{$i}");
        }
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
