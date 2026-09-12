<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Electronic devices', 'slug' => 'electronic-devices'],
            ['name' => 'Clothing', 'slug' => 'clothing'],
            ['name' => 'Footwear & Shoes', 'slug' => 'footwear-shoes'],
            ['name' => 'Food & Beverages', 'slug' => 'food-beverages'],
            ['name' => 'Home & Living', 'slug' => 'home-living'],
            ['name' => 'Sports & Fitness', 'slug' => 'sports-fitness'],
            ['name' => 'Garden tools', 'slug' => 'garden-tools'],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(
                ['slug' => $cat['slug']],
                ['name' => $cat['name']]
            );
        }
    }
}
