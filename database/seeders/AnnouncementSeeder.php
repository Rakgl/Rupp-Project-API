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
        ]);
    }
}
