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
            // Product Categories
            [
                'name' => ['en' => 'Dog Supplies', 'kh' => 'គ្រឿងផ្គត់ផ្គង់ឆ្កែ', 'zh' => '狗用品'],
                'description' => ['en' => 'Food, toys, and accessories for dogs', 'kh' => 'ចំណី ប្រដាប់ក្មេងលេង និងគ្រឿងបន្លាស់សម្រាប់ឆ្កែ', 'zh' => '狗狗的食物、玩具和配件'],
                'slug' => 'dog-supplies',
                'type' => 'PRODUCT',
                'image_url' => 'https://images.unsplash.com/photo-1583337130417-3346a1be7dee?auto=format&fit=crop&w=800&q=80',
                'status' => 'ACTIVE'
            ],
            [
                'name' => ['en' => 'Cat Supplies', 'kh' => 'គ្រឿងផ្គត់ផ្គង់ឆ្មា', 'zh' => '猫用品'],
                'description' => ['en' => 'Food, toys, and accessories for cats', 'kh' => 'ចំណី ប្រដាប់ក្មេងលេង និងគ្រឿងបន្លាស់សម្រាប់ឆ្មា', 'zh' => '猫咪的食物、玩具和配件'],
                'slug' => 'cat-supplies',
                'type' => 'PRODUCT',
                'image_url' => 'https://images.unsplash.com/photo-1514888286974-6c03e2ca1dba?auto=format&fit=crop&w=800&q=80',
                'status' => 'ACTIVE'
            ],
            [
                'name' => ['en' => 'Fish Supplies', 'kh' => 'គ្រឿងផ្គត់ផ្គង់ត្រី', 'zh' => '鱼类用品'],
                'description' => ['en' => 'Tanks, food, and filters for fish', 'kh' => 'អាង ចំណី និងតម្រងសម្រាប់ត្រី', 'zh' => '鱼缸、食物和过滤器'],
                'slug' => 'fish-supplies',
                'type' => 'PRODUCT',
                'image_url' => 'https://images.unsplash.com/photo-1522069169874-c58ec4b76be5?auto=format&fit=crop&w=800&q=80',
                'status' => 'ACTIVE'
            ],
            [
                'name' => ['en' => 'Bird Supplies', 'kh' => 'គ្រឿងផ្គត់ផ្គង់បក្សី', 'zh' => '鸟类用品'],
                'description' => ['en' => 'Cages, food, and toys for birds', 'kh' => 'ទ្រុង ចំណី និងប្រដាប់ក្មេងលេងសម្រាប់បក្សី', 'zh' => '鸟笼、食物和玩具'],
                'slug' => 'bird-supplies',
                'type' => 'PRODUCT',
                'image_url' => 'https://images.unsplash.com/photo-1552728089-57bdde30eba3?auto=format&fit=crop&w=800&q=80',
                'status' => 'ACTIVE'
            ],
            [
                'name' => ['en' => 'Small Pet Supplies', 'kh' => 'គ្រឿងផ្គត់ផ្គង់សត្វចិញ្ចឹមតូចៗ', 'zh' => '小型宠物用品'],
                'description' => ['en' => 'Supplies for rabbits, hamsters, and other small pets', 'kh' => 'គ្រឿងផ្គត់ផ្គង់សម្រាប់ទន្សាយ ហាំស្ទ័រ និងសត្វចិញ្ចឹមតូចៗផ្សេងទៀត', 'zh' => '兔子、仓鼠和其他小型宠物的用品'],
                'slug' => 'small-pet-supplies',
                'type' => 'PRODUCT',
                'image_url' => 'https://images.unsplash.com/photo-1585110396054-c8182a7a8ce0?auto=format&fit=crop&w=800&q=80',
                'status' => 'ACTIVE'
            ],

            // Pet Categories
            [
                'name' => ['en' => 'Dogs', 'kh' => 'ឆ្កែ', 'zh' => '狗'],
                'description' => ['en' => 'Various breeds of dogs', 'kh' => 'ពូជឆ្កែផ្សេងៗគ្នា', 'zh' => '各种品种的狗'],
                'slug' => 'dogs',
                'type' => 'PET',
                'image_url' => 'https://images.unsplash.com/photo-1517849845537-4d257902454a?auto=format&fit=crop&w=800&q=80',
                'status' => 'ACTIVE'
            ],
            [
                'name' => ['en' => 'Cats', 'kh' => 'ឆ្មា', 'zh' => '猫'],
                'description' => ['en' => 'Various breeds of cats', 'kh' => 'ពូជឆ្មាផ្សេងៗគ្នា', 'zh' => '各种品种的猫'],
                'slug' => 'cats',
                'type' => 'PET',
                'image_url' => 'https://images.unsplash.com/photo-1495360010541-f48722b34f7d?auto=format&fit=crop&w=800&q=80',
                'status' => 'ACTIVE'
            ],
            [
                'name' => ['en' => 'Fish', 'kh' => 'ត្រី', 'zh' => '鱼'],
                'description' => ['en' => 'Various types of pet fish', 'kh' => 'ប្រភេទត្រីចិញ្ចឹមផ្សេងៗគ្នា', 'zh' => '各种类型的宠物鱼'],
                'slug' => 'fish',
                'type' => 'PET',
                'image_url' => 'https://images.unsplash.com/photo-1524704796725-9fc3044a58b2?auto=format&fit=crop&w=800&q=80',
                'status' => 'ACTIVE'
            ],
            [
                'name' => ['en' => 'Birds', 'kh' => 'បក្សី', 'zh' => '鸟'],
                'description' => ['en' => 'Various types of pet birds', 'kh' => 'ប្រភេទបក្សីចិញ្ចឹមផ្សេងៗគ្នា', 'zh' => '各种类型的宠物鸟'],
                'slug' => 'birds',
                'type' => 'PET',
                'image_url' => 'https://images.unsplash.com/photo-1522850949506-5855141d15c0?auto=format&fit=crop&w=800&q=80',
                'status' => 'ACTIVE'
            ],
            [
                'name' => ['en' => 'Rabbits', 'kh' => 'ទន្សាយ', 'zh' => '兔子'],
                'description' => ['en' => 'Various breeds of rabbits', 'kh' => 'ពូជទន្សាយផ្សេងៗគ្នា', 'zh' => '各种品种的兔子'],
                'slug' => 'rabbits',
                'type' => 'PET',
                'image_url' => 'https://images.unsplash.com/photo-1518717758536-85ae29035b6d?auto=format&fit=crop&w=800&q=80',
                'status' => 'ACTIVE'
            ],
            [
                'name' => ['en' => 'Reptiles', 'kh' => 'ល្មូន', 'zh' => '爬行动物'],
                'description' => ['en' => 'Lizards, snakes, and other reptiles', 'kh' => 'បង្គួយ ពស់ និងសត្វល្មូនផ្សេងៗទៀត', 'zh' => '蜥蜴、蛇和其他爬行动物'],
                'slug' => 'reptiles',
                'type' => 'PET',
                'image_url' => 'https://images.unsplash.com/photo-1504450758481-7338eba7524a?auto=format&fit=crop&w=800&q=80',
                'status' => 'ACTIVE'
            ],
            [
                'name' => ['en' => 'Exotic Pets', 'kh' => 'សត្វចិញ្ចឹមប្លែកៗ', 'zh' => '奇异宠物'],
                'description' => ['en' => 'Various exotic and unique pets', 'kh' => 'សត្វចិញ្ចឹមប្លែកៗ និងប្លែកពីគេផ្សេងៗគ្នា', 'zh' => '各种奇异和独特的宠物'],
                'slug' => 'exotic-pets',
                'type' => 'PET',
                'image_url' => 'https://images.unsplash.com/photo-1544390906-560bb6cb8039?auto=format&fit=crop&w=800&q=80',
                'status' => 'ACTIVE'
            ],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(
                ['slug' => $cat['slug']],
                $cat
            );
        }
    }
}
