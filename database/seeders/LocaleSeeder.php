<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LocaleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('locales')->insert([
            [
                'id' => Str::uuid(),
                'name' => "English",
                'code' => 'en',
                'iso' => "en-gb",
                'default' => true
            ],
            [
                'id' => Str::uuid(),
                'name' => "Khmer",
                'code' => 'kh',
                'iso' => "km",
                'default' => false
            ],
        ]);
    }
}
