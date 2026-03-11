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
                'category_slug' => 'dog-supplies',
                'name' => [
                    'en' => 'Premium Dog Food',
                    'kh' => 'ចំណីឆ្កែគុណភាពខ្ពស់',
                    'zh' => '优质狗粮'
                ],
                'slug' => Str::slug('Premium Dog Food'),
                'description' => [
                    'en' => 'Nutritious and balanced diet for adult dogs',
                    'kh' => 'របបអាហារដែលមានជីវជាតិ និងតុល្យភាពសម្រាប់ឆ្កែពេញវ័យ',
                    'zh' => '成犬的营养均衡饮食'
                ],
                'attributes' => json_encode([
                    'brand' => 'Royal Canin',
                    'weight' => '10kg',
                    'flavor' => 'Chicken',
                ]),
                'price' => 45.00,
                'sku' => 'DOG-FOOD-001',
                'image_url' => 'https://images.unsplash.com/photo-1583337130417-3346a1be7dee?auto=format&fit=crop&w=500&q=80',
                'status' => 'ACTIVE'
            ],
            [
                'category_slug' => 'cat-supplies',
                'name' => [
                    'en' => 'Interactive Cat Toy',
                    'kh' => 'ប្រដាប់ក្មេងលេងឆ្មា',
                    'zh' => '互动猫玩具'
                ],
                'slug' => Str::slug('Interactive Cat Toy'),
                'description' => [
                    'en' => 'A fun toy to keep your cat active and entertained',
                    'kh' => 'ប្រដាប់ក្មេងលេងដ៏រីករាយដើម្បីឱ្យឆ្មារបស់អ្នកមានសកម្មភាព និងការកម្សាន្ត',
                    'zh' => '一个让您的猫保持活跃和娱乐的有趣玩具'
                ],
                'attributes' => json_encode([
                    'type' => 'Laser',
                    'color' => 'Red',
                    'material' => 'Plastic',
                ]),
                'price' => 12.50,
                'sku' => 'CAT-TOY-001',
                'image_url' => 'https://images.unsplash.com/photo-1545641457-19ad0e0ccb64?auto=format&fit=crop&w=500&q=80',
                'status' => 'ACTIVE'
            ],
            [
                'category_slug' => 'fish-supplies',
                'name' => [
                    'en' => 'LED Fish Tank Filter',
                    'kh' => 'តម្រងអាងត្រី LED',
                    'zh' => 'LED鱼缸过滤器'
                ],
                'slug' => Str::slug('LED Fish Tank Filter'),
                'description' => [
                    'en' => 'High-efficiency filter for small to medium fish tanks',
                    'kh' => 'តម្រងដែលមានប្រសិទ្ធភាពខ្ពស់សម្រាប់អាងត្រីតូចទៅមធ្យម',
                    'zh' => '适用于中小型鱼缸的高效过滤器'
                ],
                'attributes' => json_encode([
                    'power' => '5W',
                    'flow_rate' => '300L/H',
                ]),
                'price' => 25.00,
                'sku' => 'FISH-FILTER-001',
                'image_url' => 'https://images.unsplash.com/photo-1522069169874-c58ec4b76be5?auto=format&fit=crop&w=500&q=80',
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
                        'attributes' => $prod['attributes'],
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
