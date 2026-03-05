<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = Category::all();
        
        if ($categories->isEmpty()) {
            return;
        }

        $products = [
            [
                'category_slug' => 'food',
                'name' => 'Premium Dog Kibble',
                'slug' => Str::slug('Premium Dog Kibble'),
                'description' => 'High protein dog food',
                'price' => 35.50,
                'sku' => 'SKU-FOOD-001',
                'image_url' => 'https://example.com/images/product-kibble.png',
                'status' => 'ACTIVE'
            ],
            [
                'category_slug' => 'food',
                'name' => 'Cat Wet Food Canned',
                'slug' => Str::slug('Cat Wet Food Canned'),
                'description' => 'Delicious tuna recipe',
                'price' => 2.50,
                'sku' => 'SKU-FOOD-002',
                'image_url' => 'https://example.com/images/product-cat-wet.png',
                'status' => 'ACTIVE'
            ],
            [
                'category_slug' => 'toys',
                'name' => 'Squeaky Rubber Bone',
                'slug' => Str::slug('Squeaky Rubber Bone'),
                'description' => 'Durable rubber toy',
                'price' => 8.99,
                'sku' => 'SKU-TOYS-001',
                'image_url' => 'https://example.com/images/product-bone.png',
                'status' => 'ACTIVE'
            ],
            [
                'category_slug' => 'accessories',
                'name' => 'Adjustable Nylon Collar',
                'slug' => Str::slug('Adjustable Nylon Collar'),
                'description' => 'Reflective collar',
                'price' => 12.00,
                'sku' => 'SKU-ACCS-001',
                'image_url' => 'https://example.com/images/product-collar.png',
                'status' => 'ACTIVE'
            ],
            [
                'category_slug' => 'health-and-wellness',
                'name' => 'Flea & Tick Shampoo',
                'slug' => Str::slug('Flea Tick Shampoo'),
                'description' => 'Gentle on skin',
                'price' => 15.00,
                'sku' => 'SKU-HLTH-001',
                'image_url' => 'https://example.com/images/product-shampoo.png',
                'status' => 'ACTIVE'
            ]
        ];

        foreach ($products as $prod) {
            $cat = $categories->where('slug', $prod['category_slug'])->first();
            
            if ($cat) {
                Product::updateOrCreate(
                    ['slug' => $prod['slug']],
                    [
                        'category_id' => $cat->id,
                        'name' => $prod['name'],
                        'description' => $prod['description'],
                        'price' => $prod['price'],
                        'sku' => $prod['sku'],
                        'image_url' => $prod['image_url'],
                        'status' => $prod['status']
                    ]
                );
            }
        }
    }
}
