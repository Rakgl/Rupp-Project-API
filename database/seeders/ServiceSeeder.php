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
                'name' => ['en' => 'Full Grooming', 'kh' => 'សេវាកម្មកាត់សម្អាតរោមពេញលេញ', 'zh' => '全套美容'],
                'description' => ['en' => 'Complete hair cut, bath, nail trimming, and ear cleaning.', 'kh' => 'កាត់រោម ងូតទឹក កាត់ក្រចក និងសម្អាតត្រចៀកឱ្យបានពេញលេញ។', 'zh' => '全套理发、洗澡、剪指甲和洗耳朵。'],
                'price' => 25.00,
                'duration_minutes' => 90,
                'image_url' => 'https://images.unsplash.com/photo-1604675879599-2d5f1fdfa9e7?auto=format&fit=crop&w=800&q=80',
                'status' => 'ACTIVE'
            ],
            [
                'name' => ['en' => 'Pet Bath & Brush', 'kh' => 'ងូតទឹក និងសិតរោមសត្វចិញ្ចឹម', 'zh' => '宠物洗澡和梳毛'],
                'description' => ['en' => 'Professional bath with premium shampoo and thorough brushing.', 'kh' => 'ងូតទឹកដោយប្រើសាប៊ូពិសេស និងការសិតរោមឱ្យបានស្អាតល្អ។', 'zh' => '使用优质洗发水进行专业洗澡并彻底梳理。'],
                'price' => 15.00,
                'duration_minutes' => 45,
                'image_url' => 'https://images.unsplash.com/photo-1560743641-3914f2c45636?auto=format&fit=crop&w=800&q=80',
                'status' => 'ACTIVE'
            ],
            [
                'name' => ['en' => 'Nail Trimming', 'kh' => 'ការកាត់ក្រចក', 'zh' => '修剪指甲'],
                'description' => ['en' => 'Safe and quick nail trimming for your pets.', 'kh' => 'ការកាត់ក្រចកដោយសុវត្ថិភាព និងរហ័សសម្រាប់សត្វចិញ្ចឹមរបស់អ្នក។', 'zh' => '为您宠物进行安全快速的修剪指甲。'],
                'price' => 5.00,
                'duration_minutes' => 15,
                'image_url' => 'https://images.unsplash.com/photo-1601758174114-e711dde35264?auto=format&fit=crop&w=800&q=80',
                'status' => 'ACTIVE'
            ],
            [
                'name' => ['en' => 'Vaccination', 'kh' => 'ការចាក់វ៉ាក់សាំង', 'zh' => '疫苗接种'],
                'description' => ['en' => 'Essential vaccinations to keep your pets healthy.', 'kh' => 'ការចាក់វ៉ាក់សាំងចាំបាច់ ដើម្បីរក្សាសុខភាពសត្វចិញ្ចឹមរបស់អ្នក។', 'zh' => '必不可少的疫苗接种以保持您的宠物健康。'],
                'price' => 30.00,
                'duration_minutes' => 30,
                'image_url' => 'https://images.unsplash.com/photo-1628009368231-7bb7cfcb0def?auto=format&fit=crop&w=800&q=80',
                'status' => 'ACTIVE'
            ],
            [
                'name' => ['en' => 'Dental Care', 'kh' => 'ការថែទាំធ្មេញ', 'zh' => '牙科保健'],
                'description' => ['en' => 'Professional teeth cleaning and oral check-up.', 'kh' => 'ការសម្អាតធ្មេញប្រកបដោយវិជ្ជាជីវៈ និងការពិនិត្យមាត់ធ្មេញ។', 'zh' => '专业洁牙和口腔检查。'],
                'price' => 40.00,
                'duration_minutes' => 60,
                'image_url' => 'https://images.unsplash.com/photo-1587300003388-59208cc962cb?auto=format&fit=crop&w=800&q=80',
                'status' => 'ACTIVE'
            ],
            [
                'name' => ['en' => 'Veterinary Consultation', 'kh' => 'ការពិគ្រោះយោបល់ជាមួយពេទ្យសត្វ', 'zh' => '兽医咨询'],
                'description' => ['en' => 'General health check-up and consultation with a certified vet.', 'kh' => 'ការពិនិត្យសុខភាពទូទៅ និងការពិគ្រោះយោបល់ជាមួយពេទ្យសត្វដែលមានវិញ្ញាបនបត្រ។', 'zh' => '由执业兽医进行的常规健康检查和咨询。'],
                'price' => 20.00,
                'duration_minutes' => 30,
                'image_url' => 'https://images.unsplash.com/photo-1576201836106-db1758fd1c97?auto=format&fit=crop&w=800&q=80',
                'status' => 'ACTIVE'
            ],
            [
                'name' => ['en' => 'Pet Boarding (Per Night)', 'kh' => 'សេវាកម្មស្នាក់នៅ (ក្នុងមួយយប់)', 'zh' => '宠物寄养（每晚）'],
                'description' => ['en' => 'Safe and comfortable overnight stay for your pets.', 'kh' => 'ការស្នាក់នៅពេញមួយយប់ដោយសុវត្ថិភាព និងផាសុកភាពសម្រាប់សត្វចិញ្ចឹមរបស់អ្នក។', 'zh' => '为您宠物提供安全舒适的过夜住宿。'],
                'price' => 15.00,
                'duration_minutes' => 1440,
                'image_url' => 'https://images.unsplash.com/photo-1548546738-8542ad3913fe?auto=format&fit=crop&w=800&q=80',
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
