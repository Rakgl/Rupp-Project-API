<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            [
                'name' => [
                    'en' => 'Full Grooming',
                    'kh' => 'សេវាកម្មកាត់សម្អាតរោមពេញលេញ',
                    'zh' => '全套美容'
                ],
                'description' => [
                    'en' => 'Complete hair cut, bath, nail trimming, and ear cleaning.',
                    'kh' => 'កាត់រោម ងូតទឹក កាត់ក្រចក និងសម្អាតត្រចៀកឱ្យបានពេញលេញ។',
                    'zh' => '全套理发、洗澡、剪指甲和洗耳朵。'
                ],
                'price' => 25.00,
                'duration_minutes' => 90,
                'image_url' => 'https://images.unsplash.com/photo-1604675879599-2d5f1fdfa9e7?auto=format&fit=crop&w=800&q=80',
                'status' => 'ACTIVE'
            ],
            [
                'name' => [
                    'en' => 'Pet Bath & Brush',
                    'kh' => 'ងូតទឹក និងសិតរោមសត្វចិញ្ចឹម',
                    'zh' => '宠物洗澡和梳毛'
                ],
                'description' => [
                    'en' => 'Professional bath with premium shampoo and thorough brushing.',
                    'kh' => 'ងូតទឹកដោយប្រើសាប៊ូពិសេស និងការសិតរោមឱ្យបានស្អាតល្អ។',
                    'zh' => '使用优质洗发水进行专业洗澡并彻底梳理。'
                ],
                'price' => 15.00,
                'duration_minutes' => 45,
                'image_url' => 'https://images.unsplash.com/photo-1560743641-3914f2c45636?auto=format&fit=crop&w=800&q=80',
                'status' => 'ACTIVE'
            ],
            [
                'name' => [
                    'en' => 'Nail Trimming',
                    'kh' => 'ការកាត់ក្រចក',
                    'zh' => '修剪指甲'
                ],
                'description' => [
                    'en' => 'Safe and quick nail trimming for your pets.',
                    'kh' => 'ការកាត់ក្រចកដោយសុវត្ថិភាព និងរហ័សសម្រាប់សត្វចិញ្ចឹមរបស់អ្នក។',
                    'zh' => '为您宠物进行安全快速的修剪指甲。'
                ],
                'price' => 5.00,
                'duration_minutes' => 15,
                'image_url' => 'https://images.unsplash.com/photo-1601758174114-e711dde35264?auto=format&fit=crop&w=800&q=80',
                'status' => 'ACTIVE'
            ],
            [
                'name' => [
                    'en' => 'Vaccination',
                    'kh' => 'ការចាក់វ៉ាក់សាំង',
                    'zh' => '疫苗接种'
                ],
                'description' => [
                    'en' => 'Essential vaccinations to keep your pets healthy.',
                    'kh' => 'ការចាក់វ៉ាក់សាំងចាំបាច់ ដើម្បីរក្សាសុខភាពសត្វចិញ្ចឹមរបស់អ្នក។',
                    'zh' => '必不可少的疫苗接种以保持您的宠物健康。'
                ],
                'price' => 30.00,
                'duration_minutes' => 30,
                'image_url' => 'https://images.unsplash.com/photo-1628009368231-7bb7cfcb0def?auto=format&fit=crop&w=800&q=80',
                'status' => 'ACTIVE'
            ],
            [
                'name' => [
                    'en' => 'Dental Care',
                    'kh' => 'ការថែទាំធ្មេញ',
                    'zh' => '牙科保健'
                ],
                'description' => [
                    'en' => 'Professional teeth cleaning and oral check-up.',
                    'kh' => 'ការសម្អាតធ្មេញប្រកបដោយវិជ្ជាជីវៈ និងការពិនិត្យមាត់ធ្មេញ។',
                    'zh' => '专业洁牙和口腔检查。'
                ],
                'price' => 40.00,
                'duration_minutes' => 60,
                'image_url' => 'https://images.unsplash.com/photo-1587300003388-59208cc962cb?auto=format&fit=crop&w=800&q=80',
                'status' => 'ACTIVE'
            ]
        ];

        foreach ($services as $service) {
            Service::updateOrCreate(
                ['name->en' => $service['name']['en']],
                $service
            );
        }
    }
}