<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => [
                    'en' => 'Dog Supplies',
                    'kh' => 'គ្រឿងផ្គត់ផ្គង់ឆ្កែ',
                    'zh' => '狗用品'
                ],
                'description' => [
                    'en' => 'Food, toys, and accessories for dogs',
                    'kh' => 'ចំណី ប្រដាប់ក្មេងលេង និងគ្រឿងបន្លាស់សម្រាប់ឆ្កែ',
                    'zh' => '狗狗的食物、玩具和配件'
                ],
                'slug' => 'dog-supplies',
                'type' => 'PRODUCT',
                'image_url' => 'https://images.unsplash.com/photo-1583337130417-3346a1be7dee?auto=format&fit=crop&w=500&q=80',
                'status' => 'ACTIVE'
            ],
            [
                'name' => [
                    'en' => 'Cat Supplies',
                    'kh' => 'គ្រឿងផ្គត់ផ្គង់ឆ្មា',
                    'zh' => '猫用品'
                ],
                'description' => [
                    'en' => 'Food, toys, and accessories for cats',
                    'kh' => 'ចំណី ប្រដាប់ក្មេងលេង និងគ្រឿងបន្លាស់សម្រាប់ឆ្មា',
                    'zh' => '猫咪的食物、玩具和配件'
                ],
                'slug' => 'cat-supplies',
                'type' => 'PRODUCT',
                'image_url' => 'https://images.unsplash.com/photo-1514888286974-6c03e2ca1dba?auto=format&fit=crop&w=500&q=80',
                'status' => 'ACTIVE'
            ],
            [
                'name' => [
                    'en' => 'Fish Supplies',
                    'kh' => 'គ្រឿងផ្គត់ផ្គង់ត្រី',
                    'zh' => '鱼类用品'
                ],
                'description' => [
                    'en' => 'Tanks, food, and filters for fish',
                    'kh' => 'អាង ចំណី និងតម្រងសម្រាប់ត្រី',
                    'zh' => '鱼缸、食物和过滤器'
                ],
                'slug' => 'fish-supplies',
                'type' => 'PRODUCT',
                'image_url' => 'https://images.unsplash.com/photo-1524704796725-9fc3044a58b2?auto=format&fit=crop&w=500&q=80',
                'status' => 'ACTIVE'
            ]
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(
                ['slug' => $cat['slug']],
                $cat
            );
        }
    }
}
