<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categoryMap = Category::pluck('id', 'slug')->toArray();

        // Fallback category ID if specific slug not found
        $defaultCatId = Category::first()->id ?? 1;

        $products = [
            [
                'name' => 'Apple Watch Series 4',
                'category_slug' => 'electronic-devices',
                'price' => 399.00,
                'stock' => 25,
                'description' => 'Smart watch with high-resolution retina display, heart rate monitor, ECG app, and water resistance up to 50 meters.',
                'image' => '1.png',
            ],
            [
                'name' => 'In-Ear Stereo Earphones',
                'category_slug' => 'electronic-devices',
                'price' => 29.99,
                'stock' => 80,
                'description' => 'High-definition wired stereo earphones featuring noise isolation, inline remote microphone, and durable tangle-free cable.',
                'image' => '2.png',
            ],
            [
                'name' => 'Quadcopter Camera Drone',
                'category_slug' => 'electronic-devices',
                'price' => 249.99,
                'stock' => 15,
                'description' => 'Foldable quadcopter drone equipped with 4K HD camera, altitude hold, GPS return home, and 30-minute flight time.',
                'image' => '3.png',
            ],
            [
                'name' => 'Lungarotti di Montefalco Red Wine',
                'category_slug' => 'food-beverages',
                'price' => 45.00,
                'stock' => 50,
                'description' => 'Full-bodied Italian red wine offering notes of ripe blackberry, violet, and subtle spices, aged in oak barrels.',
                'image' => '4.png',
            ],
            [
                'name' => 'Korbel Premium Brandy',
                'category_slug' => 'food-beverages',
                'price' => 34.99,
                'stock' => 40,
                'description' => 'Handcrafted California brandy distilled from premium wine grapes and mellowed in oak casks for exceptional smoothness.',
                'image' => '5.png',
            ],
            [
                'name' => 'The White Knight Viognier Wine',
                'category_slug' => 'food-beverages',
                'price' => 28.50,
                'stock' => 45,
                'description' => 'Crisp and elegant white wine highlighting fragrant aromas of white peach, honeysuckle, and tropical fruit with balanced acidity.',
                'image' => '6.png',
            ],
            [
                'name' => 'Women\'s Winter Parka Coat',
                'category_slug' => 'clothing',
                'price' => 159.00,
                'stock' => 30,
                'description' => 'Heavyweight hooded winter parka with luxurious faux-fur trim, fleece lining, and thermal weather-resistant shell.',
                'image' => '7.png',
            ],
            [
                'name' => 'Men\'s Quilted Down Puffer Jacket',
                'category_slug' => 'clothing',
                'price' => 129.99,
                'stock' => 35,
                'description' => 'Lightweight yet exceptionally warm down jacket with windproof fabric, secure zipper pockets, and stand collar.',
                'image' => '8.png',
            ],
            [
                'name' => 'Men\'s Sleeveless Athletic Hoodie',
                'category_slug' => 'clothing',
                'price' => 39.99,
                'stock' => 60,
                'description' => 'Performance gym sleeveless hoodie crafted from moisture-wicking stretch fabric for ultimate mobility during workouts.',
                'image' => '9.png',
            ],
            [
                'name' => 'Nike P-6000 Running Sneakers',
                'category_slug' => 'footwear-shoes',
                'price' => 110.00,
                'stock' => 40,
                'description' => 'Vintage-inspired lifestyle running shoes featuring breathable mesh uppers, horizontal overlays, and foam cushioning.',
                'image' => '10.png',
            ],
            [
                'name' => 'Nike Adapt Auto Max Futuristic Sneakers',
                'category_slug' => 'footwear-shoes',
                'price' => 220.00,
                'stock' => 20,
                'description' => 'Cutting-edge power-lacing athletic shoes with custom fit motorized lacing, Max Air unit, and LED accent lighting.',
                'image' => '11.png',
            ],
            [
                'name' => 'Nike Joyride Run Flyknit Shoes',
                'category_slug' => 'footwear-shoes',
                'price' => 140.00,
                'stock' => 30,
                'description' => 'Revolutionary running shoes with tiny foam beads underfoot engineered to conform to your stride for unmatched comfort.',
                'image' => '12.png',
            ],
            [
                'name' => 'Nike Adapt BB High-Top Basketball Shoes',
                'category_slug' => 'footwear-shoes',
                'price' => 250.00,
                'stock' => 18,
                'description' => 'Smart basketball sneakers engineered with automated fit power-laces for lock-down support and responsive court feel.',
                'image' => '13.png',
            ],
            [
                'name' => 'Fjällräven Kånken Classic Backpack',
                'category_slug' => 'clothing',
                'price' => 79.99,
                'stock' => 55,
                'description' => 'Iconic Scandinavian backpack crafted from water-resistant Vinylon F fabric with convertible straps and seat pad.',
                'image' => '14.png',
            ],
            [
                'name' => 'Bose Noise Cancelling Headphones 700',
                'category_slug' => 'electronic-devices',
                'price' => 329.00,
                'stock' => 25,
                'description' => 'Critically acclaimed wireless Bluetooth over-ear headphones featuring 11 levels of active noise cancellation and crystal-clear calls.',
                'image' => '15.png',
            ],
            [
                'name' => 'Amazon Echo Dot Smart Speaker',
                'category_slug' => 'electronic-devices',
                'price' => 49.99,
                'stock' => 70,
                'description' => 'Compact voice-controlled smart speaker with Alexa, rich vocals, balanced bass, and smart home control capabilities.',
                'image' => '16.png',
            ],
            [
                'name' => 'Modern Ceramic Duo Vases',
                'category_slug' => 'home-living',
                'price' => 65.00,
                'stock' => 25,
                'description' => 'Handcrafted contemporary two-tone ceramic decorative vases, perfect for floral arrangements or modern interior styling.',
                'image' => '17.png',
            ],
            [
                'name' => 'Eames Style Molded Dining Chair',
                'category_slug' => 'home-living',
                'price' => 89.00,
                'stock' => 40,
                'description' => 'Iconic mid-century modern accent chair featuring an ergonomic polypropylene seat and geometric Eiffel wooden legs.',
                'image' => '18.png',
            ],
            [
                'name' => 'Retro Yellow Enamel Tea Kettle',
                'category_slug' => 'home-living',
                'price' => 38.00,
                'stock' => 35,
                'description' => 'Stove-top enamel on steel whistling tea kettle with heat-resistant handle and quick boil design.',
                'image' => '19.png',
            ],
            [
                'name' => 'Non-Slip Yoga Mat & Foam Blocks',
                'category_slug' => 'sports-fitness',
                'price' => 49.50,
                'stock' => 50,
                'description' => 'Premium 6mm cushioned non-slip exercise yoga mat with two high-density EVA foam support blocks and carrying strap.',
                'image' => '20.png',
            ],
            [
                'name' => 'Apple AirPods with Charging Case',
                'category_slug' => 'electronic-devices',
                'price' => 159.00,
                'stock' => 65,
                'description' => 'Wireless Bluetooth earphones with Apple H1 headphone chip, voice-activated Siri, and compact portable charging case.',
                'image' => '21.png',
            ],
            [
                'name' => 'Interactive Smart Robot Toy',
                'category_slug' => 'electronic-devices',
                'price' => 74.99,
                'stock' => 30,
                'description' => 'Remote-controlled companion robot for kids with LED expressive screen, dancing moves, obstacle sensing, and wheel locomotion.',
                'image' => '22.png',
            ],
        ];

        foreach ($products as $item) {
            $categoryId = $categoryMap[$item['category_slug']] ?? $defaultCatId;

            Product::updateOrCreate(
                ['name' => $item['name']],
                [
                    'category_id' => $categoryId,
                    'price' => $item['price'],
                    'stock' => $item['stock'],
                    'description' => $item['description'],
                    'image' => $item['image'],
                ]
            );
        }

        // Ensure any pre-existing products also have a valid image assigned
        $fallbackImages = [
            4 => '2.png',
            5 => '1.png',
            6 => '15.png',
            7 => '1.png',
        ];

        foreach ($fallbackImages as $id => $img) {
            Product::where('id', $id)->whereNull('image')->update(['image' => $img]);
        }
    }
}
