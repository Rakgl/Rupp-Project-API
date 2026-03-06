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
                'category_slug' => 'dogs',
                'name' => [
                    'en' => 'Pug',
                    'kh' => 'ឆ្កែប៉ិប',
                    'zh' => '哈巴狗'
                ],
                'slug' => Str::slug('Pug'),
                'description' => [
                    'en' => 'A very cute dog did not bite or having any aggression',
                    'kh' => 'ឆ្កែដែលគួរឱ្យស្រលាញ់មិនខាំ ឬមានភាពកាចសាហាវ',
                    'zh' => '一只非常可爱的狗，不会咬人或有任何攻击性'
                ],
                'attributes' => json_encode([
                    'name' => 'Goji',
                    'gender' => 'Male',
                    'age' => '8',
                    'color' => 'Brown',
                ]),
                'price' => 124.90,
                'sku' => 'SKU-DOGS-PUG',
                'image_url' => 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/24.png', // Placeholder URL
                'status' => 'ACTIVE'
            ],
            [
                'category_slug' => 'cats',
                'name' => [
                    'en' => 'Snow Leopard',
                    'kh' => 'ខ្លារខិនព្រិល',
                    'zh' => '雪豹'
                ],
                'slug' => Str::slug('Snow Leopard'),
                'description' => [
                    'en' => 'A beautiful snow leopard hybrid cat',
                    'kh' => 'ខ្លារខិនព្រិលដ៏ស្រស់ស្អាត',
                    'zh' => '美丽的雪豹'
                ],
                'attributes' => json_encode([
                    'name' => 'Luna',
                    'gender' => 'Female',
                    'age' => '2',
                    'color' => 'Spotted White',
                ]),
                'price' => 324.80,
                'sku' => 'SKU-CATS-SNOW',
                'image_url' => 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/53.png', // Placeholder URL
                'status' => 'ACTIVE'
            ],
            [
                'category_slug' => 'dogs',
                'name' => [
                    'en' => 'Corgy',
                    'kh' => 'ខគី',
                    'zh' => '柯基'
                ],
                'slug' => Str::slug('Corgy'),
                'description' => [
                    'en' => 'Energetic and friendly Corgy',
                    'kh' => 'ឆ្កែខគីដ៏រួសរាយ',
                    'zh' => '活泼友好的柯基'
                ],
                'attributes' => json_encode([
                    'name' => 'Ein',
                    'gender' => 'Male',
                    'age' => '1',
                    'color' => 'Orange/White',
                ]),
                'price' => 14.60,
                'sku' => 'SKU-DOGS-CORG',
                'image_url' => 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/133.png', // Placeholder URL
                'status' => 'ACTIVE'
            ],
            [
                'category_slug' => 'fish',
                'name' => [
                    'en' => 'Gold fish',
                    'kh' => 'ត្រីមាស',
                    'zh' => '金鱼'
                ],
                'slug' => Str::slug('Gold fish'),
                'description' => [
                    'en' => 'Classic and beautiful gold fish',
                    'kh' => 'ត្រីមាសដ៏ស្រស់ស្អាត',
                    'zh' => '经典美丽的金鱼'
                ],
                'attributes' => json_encode([
                    'name' => 'Nemo',
                    'gender' => 'Unknown',
                    'age' => '1',
                    'color' => 'Gold',
                ]),
                'price' => 4.50,
                'sku' => 'SKU-FISH-GOLD',
                'image_url' => 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/118.png', // Placeholder URL
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
                        'attributes' => $prod['attributes'], // Make sure to decode/encode properly if casting is failing during seed
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
