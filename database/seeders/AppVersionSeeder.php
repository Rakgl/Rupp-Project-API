<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AppVersionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
		$versions = [
            [
                'id' => Str::uuid(),
                'announcement_id' => null,
                'platform' => 'IOS',
                'latest_version' => '1.0.2',
                'update_url' => null,
                'force_update' => false,
                'message' => 'New version available for iOS! Update now to get the latest features.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'announcement_id' => null,
                'platform' => 'ANDROID',
                'latest_version' => '1.0.2',
                'update_url' => null,
                'force_update' => false,
                'message' => 'New version available for Android! Update now to get the latest features.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('app_versions')->insert($versions);
    }
}
