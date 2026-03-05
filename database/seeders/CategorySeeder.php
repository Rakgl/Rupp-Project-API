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
                    'en' => 'Food',
                    'kh' => 'អាហារ',
                    'zh' => '食物'
                ],
                'description' => [
                    'en' => 'Food',
                    'kh' => 'អាហារ',
                    'zh' => '食物'
                ],
                'slug' => Str::slug('Food'),
                'image_url' => 'https://example.com/images/category-food.png',
                'status' => 'ACTIVE'
            ],
            [
                'name' => [
                    'en' => 'Toys',
                    'kh' => 'ប្រដាប់ក្មេងលេង',
                    'zh' => '玩具'
                ],
                'description' => [
                    'en' => 'Toys',
                    'kh' => 'ប្រដាប់ក្មេងលេង',
                    'zh' => '玩具'
                ],
                'slug' => Str::slug('Toys'),
                'image_url' => 'https://example.com/images/category-toys.png',
                'status' => 'ACTIVE'
            ],
            [
                'name' => [
                    'en' => 'Accessories',
                    'kh' => 'គ្រឿងបន្លាស់',
                    'zh' => '配件'
                ],
                'description' => [
                    'en' => 'Accessories',
                    'kh' => 'គ្រឿងបន្លាស់',
                    'zh' => '配件'
                ],
                'slug' => Str::slug('Accessories'),
                'image_url' => 'https://example.com/images/category-accessories.png',
                'status' => 'ACTIVE'
            ],
            [
                'name' => [
                    'en' => 'Health & Wellness',
                    'kh' => 'សុខភាព និង ភាពរីករាយ',
                    'zh' => '健康与保健'
                ],
                'description' => [
                    'en' => 'Health & Wellness',
                    'kh' => 'សុខភាព និង ភាពរីករាយ',
                    'zh' => '健康与保健'
                ],
                'slug' => Str::slug('Health and Wellness'),
                'image_url' => 'https://example.com/images/category-health.png',
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
