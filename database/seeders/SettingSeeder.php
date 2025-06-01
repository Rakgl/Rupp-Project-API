<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('settings')->insert([
            [
                'id' => Str::uuid(),
                'setting_key' => "app_name",
                'setting_value' => "Telemedicine",
            ],
            [
                'id' => Str::uuid(),
                'setting_key' => "app_logo",
                'setting_value' => null,
            ],
            [
                'id' => Str::uuid(),
                'setting_key' => "color",
                'setting_value' => "#84b542",
            ],

        ]);
    }
}
