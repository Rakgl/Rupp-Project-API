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
                    'en' => 'Dogs',
                    'kh' => 'ឆ្កែ',
                    'zh' => '狗'
                ],
                'description' => [
                    'en' => 'Dogs',
                    'kh' => 'ឆ្កែ',
                    'zh' => '狗'
                ],
                'slug' => Str::slug('Dogs'),
                'image_url' => 'https://example.com/images/category-dogs.png',
                'status' => 'ACTIVE'
            ],
            [
                'name' => [
                    'en' => 'Cats',
                    'kh' => 'ឆ្មា',
                    'zh' => '猫'
                ],
                'description' => [
                    'en' => 'Cats',
                    'kh' => 'ឆ្មា',
                    'zh' => '猫'
                ],
                'slug' => Str::slug('Cats'),
                'image_url' => 'https://example.com/images/category-cats.png',
                'status' => 'ACTIVE'
            ],
            [
                'name' => [
                    'en' => 'Fish',
                    'kh' => 'ត្រី',
                    'zh' => '鱼'
                ],
                'description' => [
                    'en' => 'Fish',
                    'kh' => 'ត្រី',
                    'zh' => '鱼'
                ],
                'slug' => Str::slug('Fish'),
                'image_url' => 'https://example.com/images/category-fish.png',
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
