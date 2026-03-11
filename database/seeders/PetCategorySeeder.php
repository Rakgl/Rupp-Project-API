<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PetCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => ['en' => 'Dog', 'kh' => 'ឆ្កែ', 'zh' => '狗'],
                'slug' => 'dog',
                'type' => 'PET',
                'status' => 'ACTIVE'
            ],
            [
                'name' => ['en' => 'Cat', 'kh' => 'ឆ្មា', 'zh' => '猫'],
                'slug' => 'cat',
                'type' => 'PET',
                'status' => 'ACTIVE'
            ],
            [
                'name' => ['en' => 'Bird', 'kh' => 'បក្សី', 'zh' => '鸟'],
                'slug' => 'bird',
                'type' => 'PET',
                'status' => 'ACTIVE'
            ],
            [
                'name' => ['en' => 'Fish', 'kh' => 'ត្រី', 'zh' => '鱼'],
                'slug' => 'fish',
                'type' => 'PET',
                'status' => 'ACTIVE'
            ],
            [
                'name' => ['en' => 'Reptile', 'kh' => 'ល្មូន', 'zh' => '爬行动物'],
                'slug' => 'reptile',
                'type' => 'PET',
                'status' => 'ACTIVE'
            ],
            [
                'name' => ['en' => 'Small Mammal', 'kh' => 'សត្វចិញ្ចឹមតូចៗ', 'zh' => '小型哺乳动物'],
                'slug' => 'small-mammal',
                'type' => 'PET',
                'status' => 'ACTIVE'
            ],
            [
                'name' => ['en' => 'Other / Exotic Pets', 'kh' => 'សត្វចិញ្ចឹមផ្សេងៗ', 'zh' => '其他/奇异宠物'],
                'slug' => 'other-exotic-pets',
                'type' => 'PET',
                'status' => 'ACTIVE'
            ],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(
                ['slug' => $cat['slug']],
                [
                    'name' => $cat['name'],
                    'type' => $cat['type'],
                    'status' => $cat['status'],
                    'description' => $cat['name'], // Default description to name
                ]
            );
        }
    }
}
