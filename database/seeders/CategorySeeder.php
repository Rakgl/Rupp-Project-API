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
                'description' => [
                    'en' => 'A comprehensive selection of premium dog food, interactive toys, comfortable beds, grooming tools, and training accessories to keep your canine companion healthy and happy.', 
                    'kh' => 'ជម្រើសដ៏ទូលំទូលាយនៃចំណីឆ្កែគុណភាពខ្ពស់ ប្រដាប់ក្មេងលេង កន្លែងគេងដ៏មានផាសុកភាព ឧបករណ៍ថែរក្សាសម្ផស្ស និងគ្រឿងបន្លាស់សម្រាប់ហ្វឹកហាត់ ដើម្បីជួយឱ្យសត្វចិញ្ចឹមរបស់អ្នកមានសុខភាពល្អ និងរីករាយ។', 
                    'zh' => '全面精选的优质狗粮、互动玩具、舒适的狗床、美容工具和训练配件，让您的爱犬保持健康快乐。'
                ],
                'slug' => 'dog-supplies',
                'type' => 'PRODUCT',
                'image_url' => 'https://images.unsplash.com/photo-1583337130417-3346a1be7dee?auto=format&fit=crop&w=800&q=80',
                'status' => 'ACTIVE'
            ],
            [
                'name' => ['en' => 'Cat Supplies', 'kh' => 'គ្រឿងផ្គត់ផ្គង់ឆ្មា', 'zh' => '猫用品'],
                'description' => [
                    'en' => 'Everything your feline friend needs, including nutritious cat food, scratching posts, cozy beds, litter boxes, and engaging toys for endless entertainment.', 
                    'kh' => 'រាល់តម្រូវការសម្រាប់សត្វឆ្មារបស់អ្នក រួមមានចំណីដែលសំបូរទៅដោយជីវជាតិ បង្គោលសម្រាប់ខ្វាច កន្លែងគេងដ៏កក់ក្តៅ ប្រអប់ខ្សាច់ និងប្រដាប់ក្មេងលេងសម្រាប់កម្សាន្ត។', 
                    'zh' => '您的猫咪朋友所需的一切，包括营养丰富的猫粮、猫抓板、舒适的猫窝、猫砂盆以及带来无尽欢乐的趣味玩具。'
                ],
                'slug' => 'cat-supplies',
                'type' => 'PRODUCT',
                'image_url' => 'https://images.unsplash.com/photo-1514888286974-6c03e2ca1dba?auto=format&fit=crop&w=800&q=80',
                'status' => 'ACTIVE'
            ],
            [
                'name' => ['en' => 'Fish Supplies', 'kh' => 'គ្រឿងផ្គត់ផ្គង់ត្រី', 'zh' => '鱼类用品'],
                'description' => [
                    'en' => 'Create the perfect aquatic environment with our range of glass aquariums, water filters, air pumps, premium fish flakes, and beautiful aquatic decorations.', 
                    'kh' => 'បង្កើតបរិស្ថានទឹកដ៏ល្អឥតខ្ចោះជាមួយជម្រើសអាងកញ្ចក់ តម្រងទឹក ម៉ាស៊ីនបូមខ្យល់ ចំណីត្រីគុណភាពខ្ពស់ និងគ្រឿងតុបតែងអាងត្រីដ៏ស្រស់ស្អាត។', 
                    'zh' => '使用我们的玻璃鱼缸、水过滤器、气泵、优质鱼粮和精美的水族装饰品，打造完美的水生环境。'
                ],
                'slug' => 'fish-supplies',
                'type' => 'PRODUCT',
                'image_url' => 'https://images.unsplash.com/photo-1522069169874-c58ec4b76be5?auto=format&fit=crop&w=800&q=80',
                'status' => 'ACTIVE'
            ],

            // Pet Categories
            [
                'name' => ['en' => 'Dogs', 'kh' => 'ឆ្កែ', 'zh' => '狗'],
                'description' => [
                    'en' => 'Find your perfect loyal companion from our diverse selection of dog breeds, ranging from energetic playful puppies to calm and protective adult dogs.', 
                    'kh' => 'ស្វែងរកសត្វចិញ្ចឹមដ៏ស្មោះត្រង់របស់អ្នកពីជម្រើសពូជឆ្កែដ៏សំបូរបែបរបស់យើង ចាប់ពីកូនឆ្កែដែលចូលចិត្តលេង រហូតដល់ឆ្កែធំដែលចេះការពារនិងមានភាពស្ងប់ស្ងាត់។', 
                    'zh' => '从我们丰富多样的犬种中寻找您完美的忠实伴侣，无论是充满活力的顽皮小狗，还是沉稳护主的成年犬，应有尽有。'
                ],
                'slug' => 'dogs',
                'type' => 'PET',
                'image_url' => 'https://images.unsplash.com/photo-1517849845537-4d257902454a?auto=format&fit=crop&w=800&q=80',
                'status' => 'ACTIVE'
            ],
            [
                'name' => ['en' => 'Cats', 'kh' => 'ឆ្មា', 'zh' => '猫'],
                'description' => [
                    'en' => 'Discover graceful, affectionate, and independent feline companions. We offer a variety of cat breeds to suit every household and lifestyle.', 
                    'kh' => 'ស្វែងយល់ពីសត្វឆ្មាដែលមានភាពទន់ភ្លន់ គួរឱ្យស្រលាញ់ និងឯករាជ្យ។ យើងផ្តល់ជូននូវពូជឆ្មាជាច្រើនប្រភេទដែលស័ក្តិសមសម្រាប់គ្រប់គ្រួសារ និងរបៀបរស់នៅ។', 
                    'zh' => '寻找优雅、深情且独立的猫咪伴侣。我们提供多种猫咪品种，适合各种家庭和生活方式。'
                ],
                'slug' => 'cats',
                'type' => 'PET',
                'image_url' => 'https://images.unsplash.com/photo-1495360010541-f48722b34f7d?auto=format&fit=crop&w=800&q=80',
                'status' => 'ACTIVE'
            ],
            [
                'name' => ['en' => 'Fish', 'kh' => 'ត្រី', 'zh' => '鱼'],
                'description' => [
                    'en' => 'Add vibrant colors and tranquility to your home with our beautiful selection of freshwater and saltwater pet fish, perfect for beginners and aquarists.', 
                    'kh' => 'បន្ថែមពណ៌ចម្រុះនិងភាពស្ងប់ស្ងាត់ដល់គេហដ្ឋានរបស់អ្នក ជាមួយនឹងជម្រើសត្រីទឹកសាបនិងទឹកប្រៃដ៏ស្រស់ស្អាត ស័ក្តិសមបំផុតសម្រាប់អ្នកទើបចាប់ផ្តើមចិញ្ចឹមនិងអ្នកជំនាញ។', 
                    'zh' => '我们美丽的淡水和海水宠物鱼精选，为您的家增添绚丽的色彩和宁静感，非常适合初学者和水族爱好者。'
                ],
                'slug' => 'fish',
                'type' => 'PET',
                'image_url' => 'https://images.unsplash.com/photo-1524704796725-9fc3044a58b2?auto=format&fit=crop&w=800&q=80',
                'status' => 'ACTIVE'
            ],
            [
                'name' => ['en' => 'Rabbits', 'kh' => 'ទន្សាយ', 'zh' => '兔子'],
                'description' => [
                    'en' => 'Bring home a gentle and social bunny. Our collection includes various adorable rabbit breeds known for their soft fur and loving personalities.', 
                    'kh' => 'នាំយកទន្សាយដ៏ស្លូតបូតនិងរួសរាយទៅផ្ទះរបស់អ្នក។ ការប្រមូលផ្តុំរបស់យើងរួមមានពូជទន្សាយគួរឱ្យស្រលាញ់ជាច្រើន ដែលល្បីល្បាញដោយសាររោមទន់និងអត្តចរិតគួរឱ្យស្រលាញ់។', 
                    'zh' => '带一只温和且喜欢社交的小兔回家。我们的合集包括各种可爱的兔子品种，以其柔软的皮毛和讨人喜欢的性格而闻名。'
                ],
                'slug' => 'rabbits',
                'type' => 'PET',
                'image_url' => 'https://images.unsplash.com/photo-1518717758536-85ae29035b6d?auto=format&fit=crop&w=800&q=80',
                'status' => 'ACTIVE'
            ],
            [
                'name' => ['en' => 'Reptiles', 'kh' => 'ល្មូន', 'zh' => '爬行动物'],
                'description' => [
                    'en' => 'Explore the fascinating world of cold-blooded pets. We offer healthy lizards, snakes, and turtles for enthusiasts seeking unique and exotic companions.', 
                    'kh' => 'ស្វែងយល់ពីពិភពដ៏គួរឱ្យចាប់អារម្មណ៍នៃសត្វឈាមត្រជាក់។ យើងផ្តល់ជូនសត្វបង្គួយ ពស់ និងអណ្តើកដែលមានសុខភាពល្អ សម្រាប់អ្នកដែលចូលចិត្តសត្វចិញ្ចឹមប្លែកៗ។', 
                    'zh' => '探索迷人的冷血宠物世界。我们为寻求独特和异国情调伴侣的爱好者提供健康的蜥蜴、蛇和海龟。'
                ],
                'slug' => 'reptiles',
                'type' => 'PET',
                'image_url' => 'https://images.unsplash.com/photo-1504450758481-7338eba7524a?auto=format&fit=crop&w=800&q=80',
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