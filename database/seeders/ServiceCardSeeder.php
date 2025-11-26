<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ServiceCard;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ServiceCardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks to allow truncate (optional but recommended)
        Schema::disableForeignKeyConstraints();
        
        // Clear existing data
        ServiceCard::truncate();

        // Re-enable checks
        Schema::enableForeignKeyConstraints();

        $cards = [
            [
                'title' => [
                    'en' => 'Leasing you can trust, now with Autorayider',
                    'km' => 'សេវាកម្មជួលដែលអ្នកអាចទុកចិត្តបាន ជាមួយ Autorayider',
                    'zh' => '您可以信賴的租賃服務，現在有 Autorayider'
                ],
                'description' => [
                    'en' => 'the price you see is the price you get',
                    'km' => 'តម្លៃដែលអ្នកឃើញ គឺជាតម្លៃដែលអ្នកទទួលបាន',
                    'zh' => '所見即所得'
                ],
                'button_text' => [
                    'en' => 'Button',
                    'km' => 'ប៊ូតុង',
                    'zh' => '按鈕'
                ],
                'image_url' => '/images/card-image-1.png',
                'status' => 'ACTIVE',
            ],
            [
                'title' => [
                    'en' => 'Leasing you can trust, now with Autorayider',
                    'km' => 'សេវាកម្មជួលដែលអ្នកអាចទុកចិត្តបាន ជាមួយ Autorayider',
                    'zh' => '您可以信賴的租賃服務，現在有 Autorayider'
                ],
                'description' => [
                    'en' => 'the price you see is the price you get',
                    'km' => 'តម្លៃដែលអ្នកឃើញ គឺជាតម្លៃដែលអ្នកទទួលបាន',
                    'zh' => '所見即所得'
                ],
                'button_text' => [
                    'en' => 'Button',
                    'km' => 'ប៊ូតុង',
                    'zh' => '按鈕'
                ],
                'image_url' => '/images/card-image-2.png',
                'status' => 'ACTIVE',
            ],
            [
                'title' => [
                    'en' => 'Leasing you can trust, now with Autorayider',
                    'km' => 'សេវាកម្មជួលដែលអ្នកអាចទុកចិត្តបាន ជាមួយ Autorayider',
                    'zh' => '您可以信賴的租賃服務，現在有 Autorayider'
                ],
                'description' => [
                    'en' => 'the price you see is the price you get',
                    'km' => 'តម្លៃដែលអ្នកឃើញ គឺជាតម្លៃដែលអ្នកទទួលបាន',
                    'zh' => '所見即所得'
                ],
                'button_text' => [
                    'en' => 'Button',
                    'km' => 'ប៊ូតុង',
                    'zh' => '按鈕'
                ],
                'image_url' => '/images/card-image-3.png',
                'status' => 'ACTIVE',
            ],
        ];

        foreach ($cards as $card) {
            ServiceCard::updateOrCreate(
                // Argument 1: The Match Condition (MUST be a simple string, not JSON)
                ['image_url' => $card['image_url']], 

                // Argument 2: The Data to Update/Insert
                $card 
            );
        }
    }
}