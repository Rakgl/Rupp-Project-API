<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class NotificationSeeder extends Seeder
{
    public function run()
    {
        DB::table('notifications')->insert([
            [
                'id' => Str::uuid(),
                'title' => json_encode(['en' => 'Promotion Alert', 'kh' => 'ការផ្សព្វផ្សាយ']),
                'message' => json_encode(['en' => 'Get 10% off on your next transaction!', 'kh' => 'ទទួលបានការបញ្ចុះតម្លៃ 10% លើការទូទាត់ដំណាលទៅ!']),
                'type' => 'PROMOTION',
                'status' => 'UNREAD',
                'customer_id' => Str::uuid(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'title' => json_encode(['en' => 'Transaction Successful', 'kh' => 'ប្រតិបត្តិការជោគជ័យ']),
                'message' => json_encode(['en' => 'Your payment was processed successfully.', 'kh' => 'ការទូទាត់របស់អ្នកត្រូវបានដំណើរការជោគជ័យ។']),
                'type' => 'TRANSACTION',
                'status' => 'UNREAD',
                'customer_id' => Str::uuid(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'title' => json_encode(['en' => 'System Alert', 'kh' => 'ការជូនដំណឹងប្រព័ន្ធ']),
                'message' => json_encode(['en' => 'Your account password was changed.', 'kh' => 'លេខសម្ងាត់គណនីរបស់អ្នកត្រូវបានផ្លាស់ប្តូរ។']),
                'type' => 'ALERT',
                'status' => 'READ',
                'customer_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
