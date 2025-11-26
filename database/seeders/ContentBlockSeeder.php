<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ContentBlock; 

class ContentBlockSeeder extends Seeder
{
    public function run(): void
    {
        $blocks = [
            [
                'image_path' => '/storage/images/porsche-911-hero.png', 
                'title' => [
                    'en' => 'UNLOCK YOUR TRAVEL EXPERIENCE',
                    'km' => 'ដោះសោបទពិសោធន៍ធ្វើដំណើររបស់អ្នក',
                    'zh' => '解锁您的旅行体验',
                ],
                'description' => [
                    'en' => 'Pellentesque nibh. Aenean quam. In scelerisque sem at dolor. Maecenas mattis. Sed convallis tristique sem. Proin ut ligula non nisi commodo vehicula. Aliquam ut velit in est tincidunt egestas.',
                    'km' => 'អត្ថបទគំរូជាភាសាខ្មែរ។ នេះគឺជាការពិពណ៌នាអំពីបទពិសោធន៍ធ្វើដំណើរ។',
                    'zh' => '这是关于我们公司的完整描述。Pellentesque nibh. Aenean quam.',
                ],
                'booking_btn' => [
                    'en' => 'Booking Now',
                    'km' => 'កក់ឥឡូវនេះ',
                    'zh' => '现在预订',
                ],
            ],
        ];

        foreach ($blocks as $block) {
            ContentBlock::updateOrCreate(
                // 1. Search by image_path
                ['image_path' => $block['image_path']], 
                [
                    'title'        => $block['title'], 
                    'description'  => $block['description'],
                    'booking_btn'  => $block['booking_btn'],
                    'status'       => 'ACTIVE',
                ]
            );
        }
    }
}