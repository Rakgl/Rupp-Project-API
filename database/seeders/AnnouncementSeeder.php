<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AnnouncementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('announcements')->insert([
            [
                'id' => (string) \Illuminate\Support\Str::uuid(),
                'title' => json_encode([
                    'en' => 'System Maintenance Alert',
                    'kh' => 'ប្រព័ន្ធថែទាំ'
                ]),
                'message' => json_encode([
                    'en' => 'Our system will undergo maintenance on September 30th from 2 AM to 5 AM. Please be prepared for a temporary outage.',
                    'kh' => 'ប្រព័ន្ធរបស់យើងនឹងធ្វើការថែទាំនៅថ្ងៃទី 30 ខែ​កញ្ញា ចាប់ពីម៉ោង 2 ព្រឹក ដល់ 5 ព្រឹក។ សូមមានការត្រៀមខ្លួនសម្រាប់ការផ្អាកជាបណ្តោះអាសន្ន។'
                ]),
                'type' => 'SYSTEM_ALERT',
                'scheduled_at' => Carbon::now()->addMinutes(2),
                'status' => 'PENDING',
                'image' => 'system_alert.jpg',
                'created_by' => 'admin1',
                'updated_by' => null,
                'update_num' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            // [
            //     'id' => (string) \Illuminate\Support\Str::uuid(),
            //     'title' => json_encode([
            //         'en' => 'New Promotion Alert!',
            //         'kh' => 'ប្រូម៉ូសិនថ្មី!'
            //     ]),
            //     'message' => json_encode([
            //         'en' => 'Enjoy a 20% discount on all charging stations for the next week! Don’t miss out!',
            //         'kh' => 'រីករាយនឹងការបញ្ចុះតម្លៃ 20% សម្រាប់ស្ថានីយ៍សាកទាំងអស់ក្នុងសប្ដាហ៍នេះ! កុំខកខាន!'
            //     ]),
            //     'type' => 'PROMOTION',
            //     'scheduled_at' => Carbon::now()->addMinutes(10),
            //     'status' => 'PENDING',
            //     'image' => 'promotion.jpg',
            //     'created_by' => 'admin2',
            //     'updated_by' => null,
            //     'update_num' => 0,
            //     'created_at' => Carbon::now(),
            //     'updated_at' => Carbon::now()
            // ],
            // [
            //     'id' => (string) \Illuminate\Support\Str::uuid(),
            //     'title' => json_encode([
            //         'en' => 'App Update Available',
            //         'kh' => 'មានការធ្វើបច្ចុប្បន្នភាពកម្មវិធី'
            //     ]),
            //     'message' => json_encode([
            //         'en' => 'A new version of the app is now available. Please update to enjoy the latest features and improvements.',
            //         'kh' => 'ជំនាន់ថ្មីនៃកម្មវិធីនេះមានស្រាប់ហើយ។ សូមធ្វើបច្ចុប្បន្នភាពដើម្បីរីករាយនឹងលក្ខណៈពិសេសថ្មីៗ និងការកែលម្អ។'
            //     ]),
            //     'type' => 'UPDATE',
            //     'scheduled_at' => Carbon::now()->addHours(6),
            //     'status' => 'PENDING',
            //     'image' => 'update.jpg',
            //     'created_by' => 'admin3',
            //     'updated_by' => null,
            //     'update_num' => 0,
            //     'created_at' => Carbon::now(),
            //     'updated_at' => Carbon::now()
            // ]
        ]);
    }
}
