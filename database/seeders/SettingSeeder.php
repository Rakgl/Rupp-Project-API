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
        DB::table('settings')->truncate();

        DB::table('settings')->insert([
            [
                'id' => Str::uuid(),
                'setting_key' => "app_name",
                'setting_value' => "Methgo",
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
            [
                'id' => Str::uuid(),
                'setting_key' => "about_us_description",
                'setting_value' => "Methgo is a pet shop and animal shelter that have been dedicated for years into taking care of animals and turn them into a good lovely pet. For animal lover who interested and in need of a companion.",
            ],
            [
                'id' => Str::uuid(),
                'setting_key' => "latitude",
                'setting_value' => "11.5682705",
            ],
            [
                'id' => Str::uuid(),
                'setting_key' => "longitude",
                'setting_value' => "104.890690",
            ],
            [
                'id' => Str::uuid(),
                'setting_key' => "footer_note",
                'setting_value' => "Have a great day from Ferry",
            ],
        ]);
    }
}
