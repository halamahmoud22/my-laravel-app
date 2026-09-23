<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class ApiCacheTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_products_are_cached_and_invalidated_on_change(): void
    {
        $category = Category::create(['name' => 'Tech', 'slug' => 'tech']);
        $product = Product::create([
            'name' => 'Mouse',
            'price' => 25.00,
            'stock' => 10,
            'category_id' => $category->id,
        ]);

        Cache::forget('api_products_page_1');
        $this->assertFalse(Cache::has('api_products_page_1'));

        $response = $this->getJson('/api/products');
        $response->assertStatus(200);

        $this->assertTrue(Cache::has('api_products_page_1'));

        $this->getJson("/api/products/{$product->id}")->assertStatus(200);
        $this->assertTrue(Cache::has("api_product_{$product->id}"));

        $product->update(['name' => 'Wireless Mouse']);
        $this->assertFalse(Cache::has('api_products_page_1'));
        $this->assertFalse(Cache::has("api_product_{$product->id}"));
    }

    public function test_api_categories_are_cached_and_invalidated_on_change(): void
    {
        $category = Category::create(['name' => 'Gadgets', 'slug' => 'gadgets']);

        Cache::forget('api_all_categories');
        $this->assertFalse(Cache::has('api_all_categories'));

        $response = $this->getJson('/api/categories?all=1');
        $response->assertStatus(200);
        $this->assertTrue(Cache::has('api_all_categories'));

        $this->getJson("/api/categories/{$category->id}")->assertStatus(200);
        $this->assertTrue(Cache::has("api_category_{$category->id}"));

        $category->update(['name' => 'Smart Gadgets']);
        $this->assertFalse(Cache::has('api_all_categories'));
        $this->assertFalse(Cache::has("api_category_{$category->id}"));
    }
}
