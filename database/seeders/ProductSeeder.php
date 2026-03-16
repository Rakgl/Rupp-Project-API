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
        $categories = Category::where('type', 'PRODUCT')->get();
        
        if ($categories->isEmpty()) {
            return;
        }

        $products = [
            // Dog Supplies
            [
                'category_slug' => 'dog-supplies',
                'name' => ['en' => 'Royal Canin Adult Dog Food', 'kh' => 'ចំណីឆ្កែពេញវ័យ Royal Canin', 'zh' => '皇家成犬粮'],
                'description' => ['en' => 'Tailored nutrition for adult dogs to maintain health and vitality.', 'kh' => 'អាហារូបត្ថម្ភដែលសម្រិតសម្រាំងសម្រាប់ឆ្កែពេញវ័យ ដើម្បីរក្សាសុខភាព និងថាមពល។', 'zh' => '为成年犬量身定制的营养，旨在保持健康和活力。'],
                'price' => 55.00,
                'sku' => 'DOG-FOOD-001',
                'image_url' => 'https://images.unsplash.com/photo-1589924691106-073b697596cd?auto=format&fit=crop&w=800&q=80',
                'attributes' => ['brand' => 'Royal Canin', 'weight' => '10kg', 'flavor' => 'Chicken'],
            ],
            [
                'category_slug' => 'dog-supplies',
                'name' => ['en' => 'Durable Chew Toy', 'kh' => 'ប្រដាប់ក្មេងលេងឆ្កែជាប់ធន់', 'zh' => '耐咬玩具'],
                'description' => ['en' => 'High-quality rubber chew toy for aggressive chewers.', 'kh' => 'ប្រដាប់ក្មេងលេងកៅស៊ូគុណភាពខ្ពស់សម្រាប់ឆ្កែដែលចូលចិត្តខាំខ្លាំង។', 'zh' => '适用于强力咀嚼者的高品质橡胶咀嚼玩具。'],
                'price' => 12.00,
                'sku' => 'DOG-TOY-001',
                'image_url' => 'https://images.unsplash.com/photo-1576201836106-db1758fd1c97?auto=format&fit=crop&w=800&q=80',
                'attributes' => ['material' => 'Rubber', 'color' => 'Red'],
            ],
            [
                'category_slug' => 'dog-supplies',
                'name' => ['en' => 'Leather Dog Collar', 'kh' => 'ខ្សែករឆ្កែស្បែក', 'zh' => '真皮狗项圈'],
                'description' => ['en' => 'Handcrafted genuine leather collar with brass buckle.', 'kh' => 'ខ្សែករធ្វើពីស្បែកសុទ្ធដោយដៃ ជាមួយនឹងក្បាលខ្សែលង្ហិន។', 'zh' => '手工制作的真皮项圈，配有黄铜扣。'],
                'price' => 25.00,
                'sku' => 'DOG-ACC-001',
                'image_url' => 'https://images.unsplash.com/photo-1534361960057-19889db9621e?auto=format&fit=crop&w=800&q=80',
                'attributes' => ['material' => 'Leather', 'size' => 'Medium'],
            ],

            // Cat Supplies
            [
                'category_slug' => 'cat-supplies',
                'name' => ['en' => 'Meow Mix Dry Cat Food', 'kh' => 'ចំណីឆ្មាក្រៀម Meow Mix', 'zh' => 'Meow Mix 干猫粮'],
                'description' => ['en' => 'Delicious blend of ocean fish flavors that cats love.', 'kh' => 'ល្បាយដ៏ឈ្ងុយឆ្ងាញ់នៃរសជាតិត្រីសមុទ្រដែលឆ្មាចូលចិត្ត។', 'zh' => '猫咪喜爱的海洋鱼味美味混合物。'],
                'price' => 18.50,
                'sku' => 'CAT-FOOD-001',
                'image_url' => 'https://images.unsplash.com/photo-1514888286974-6c03e2ca1dba?auto=format&fit=crop&w=800&q=80',
                'attributes' => ['brand' => 'Meow Mix', 'weight' => '3kg', 'flavor' => 'Seafood'],
            ],
            [
                'category_slug' => 'cat-supplies',
                'name' => ['en' => 'Multi-Level Cat Tree', 'kh' => 'ដើមឈើឆ្មាច្រើនជាន់', 'zh' => '多层猫爬架'],
                'description' => ['en' => 'A perfect playground for your cat with scratching posts and hammocks.', 'kh' => 'កន្លែងលេងដ៏ល្អឥតខ្ចោះសម្រាប់ឆ្មារបស់អ្នក ជាមួយនឹងកន្លែងអំបោស និងអង្រឹង។', 'zh' => '为您家猫咪准备的完美游乐场，配有抓柱和吊床。'],
                'price' => 85.00,
                'sku' => 'CAT-FURN-001',
                'image_url' => 'https://images.unsplash.com/photo-1545249390-6bdfa286032f?auto=format&fit=crop&w=800&q=80',
                'attributes' => ['material' => 'Sisal & Plush', 'height' => '150cm'],
            ],
            [
                'category_slug' => 'cat-supplies',
                'name' => ['en' => 'Interactive Laser Toy', 'kh' => 'ប្រដាប់ក្មេងលេងឡាស៊ែរ', 'zh' => '互动激光玩具'],
                'description' => ['en' => 'Automatic rotating laser light to keep your cat active.', 'kh' => 'ពន្លឺឡាស៊ែរបង្វិលដោយស្វ័យប្រវត្តិ ដើម្បីឱ្យឆ្មារបស់អ្នកមានសកម្មភាព។', 'zh' => '自动旋转激光灯，让您的猫保持活跃。'],
                'price' => 15.00,
                'sku' => 'CAT-TOY-001',
                'image_url' => 'https://images.unsplash.com/photo-1548546738-8542ad3913fe?auto=format&fit=crop&w=800&q=80',
                'attributes' => ['battery' => 'AA', 'mode' => 'Automatic'],
            ],

            // Fish Supplies
            [
                'category_slug' => 'fish-supplies',
                'name' => ['en' => 'TetraMin Tropical Flakes', 'kh' => 'ចំណីត្រី TetraMin', 'zh' => 'TetraMin 热带鱼片'],
                'description' => ['en' => 'Nutrient-rich flakes for all tropical fish.', 'kh' => 'ចំណីបន្ទះដែលសំបូរទៅដោយសារធាតុចិញ្ចឹមសម្រាប់ត្រីតំបន់ត្រូពិចទាំងអស់។', 'zh' => '富含营养的薄片，适用于所有热带鱼。'],
                'price' => 8.00,
                'sku' => 'FISH-FOOD-001',
                'image_url' => 'https://images.unsplash.com/photo-1522069169874-c58ec4b76be5?auto=format&fit=crop&w=800&q=80',
                'attributes' => ['brand' => 'Tetra', 'weight' => '100g'],
            ],
            [
                'category_slug' => 'fish-supplies',
                'name' => ['en' => 'LED Aquarium Kit', 'kh' => 'អាងត្រី LED ពេញលេញ', 'zh' => 'LED 水族箱套装'],
                'description' => ['en' => '5-gallon glass aquarium with LED lighting and filter.', 'kh' => 'អាងត្រីកញ្ចក់ចំណុះ ៥ ហ្គាឡុង ជាមួយនឹងភ្លើង LED និងតម្រង។', 'zh' => '5 加仑玻璃水族箱，配有 LED 照明和过滤器。'],
                'price' => 45.00,
                'sku' => 'FISH-TANK-001',
                'image_url' => 'https://images.unsplash.com/photo-1524704796725-9fc3044a58b2?auto=format&fit=crop&w=800&q=80',
                'attributes' => ['capacity' => '5 Gallon', 'lighting' => 'LED'],
            ],

            // Bird Supplies
            [
                'category_slug' => 'bird-supplies',
                'name' => ['en' => 'Wild Bird Seed Mix', 'kh' => 'ល្បាយគ្រាប់ធញ្ញជាតិបក្សី', 'zh' => '野鸟种子混合物'],
                'description' => ['en' => 'Premium blend of seeds to attract various birds.', 'kh' => 'ល្បាយគ្រាប់ធញ្ញជាតិគុណភាពខ្ពស់ ដើម្បីទាក់ទាញបក្សីផ្សេងៗគ្នា។', 'zh' => '吸引各种鸟类的高级种子混合物。'],
                'price' => 10.50,
                'sku' => 'BIRD-FOOD-001',
                'image_url' => 'https://images.unsplash.com/photo-1552728089-57bdde30eba3?auto=format&fit=crop&w=800&q=80',
                'attributes' => ['weight' => '2kg', 'ingredients' => 'Sunflower, Millet'],
            ],

            // Small Pet Supplies
            [
                'category_slug' => 'small-pet-supplies',
                'name' => ['en' => 'Timothy Hay for Rabbits', 'kh' => 'ស្មៅ Timothy សម្រាប់ទន្សាយ', 'zh' => '兔用提摩西草'],
                'description' => ['en' => 'High-fiber hay essential for rabbit digestion.', 'kh' => 'ស្មៅដែលមានជាតិសរសៃខ្ពស់ ចាំបាច់សម្រាប់ការរំលាយអាហាររបស់ទន្សាយ។', 'zh' => '高纤维干草，对兔子的消化至关重要。'],
                'price' => 12.00,
                'sku' => 'SMALL-FOOD-001',
                'image_url' => 'https://images.unsplash.com/photo-1585110396054-c8182a7a8ce0?auto=format&fit=crop&w=800&q=80',
                'attributes' => ['type' => 'Hay', 'weight' => '1kg'],
            ],
        ];

        foreach ($products as $prod) {
            $cat = $categories->where('slug', $prod['category_slug'])->first();
            
            if ($cat) {
                Product::updateOrCreate(
                    ['sku' => $prod['sku']],
                    [
                        'category_id' => $cat->id,
                        'name' => $prod['name'],
                        'slug' => Str::slug($prod['name']['en']),
                        'description' => $prod['description'],
                        'attributes' => json_encode($prod['attributes']),
                        'price' => $prod['price'],
                        'image_url' => $prod['image_url'],
                        'status' => 'ACTIVE'
                    ]
                );
            }
        }
    }
}
