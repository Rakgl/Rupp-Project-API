<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AboutUs;

class AboutUsSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Define a static UUID so the seeder always updates the same record
        $staticId = '9b1c775c-746e-4e9f-8566-077558667634';

        AboutUs::updateOrCreate(
            // [ARGUMENT 1] Criteria to find the record (Only use ID here)
            ['id' => $staticId], 

            // [ARGUMENT 2] Data to save (Put the Arrays/JSON here)
            [
                'title' => [
                    'en' => 'OUR COMMITMENT TO YOUR COMFORT AND SATISFACTION',
                    'kh' => 'ការប្តេជ្ញាចិត្តរបស់យើងចំពោះផាសុកភាព និងការពេញចិត្តរបស់អ្នក',
                    'zh' => '我们致力于为您提供舒适和满意的服务',
                ],
                'description' => [
                    'en' => 'Pellentesque nibh. Aenean quam. In scelerisque sem at dolor. Maecenas mattis. Sed convallis tristique sem. Proin ut ligula non nisi commodo vehicula. Aliquam ut velit in est tincidunt egestas.',
                    'kh' => 'Pellentesque nibh. Aenean quam. In scelerisque sem at dolor. Maecenas mattis. Sed convallis tristique sem.',
                    'zh' => 'Pellentesque nibh. Aenean quam. In scelerisque sem at dolor. Maecenas mattis. Sed convallis tristique sem.',
                ],
                'list_text' => [
                    'en' => ['24/7 Online Booking', 'Diverse Vehicle Selection', 'Flexible pick-up and drop-off location'],
                    'kh' => ['ការកក់តាមអនឡាញ 24/7', 'ជម្រើសយានយន្តចម្រុះ', 'ទីតាំងទទួល និងដាក់ចុះដែលអាចបត់បែនបាន'],
                    'zh' => ['24/7 在线预订', '多种车辆选择', '灵活的接送地点'],
                ],
                'image_url' => 'about_us/dummy-car-person.png',
                'status' => 'ACTIVE',
                'created_by' => 'Seeder',
            ]
        );
    }
}