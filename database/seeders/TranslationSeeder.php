<?php

namespace Database\Seeders;

use App\Models\Translation;
use Illuminate\Database\Seeder;

class TranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $translations = [
           ['key' => 'account_settings', 'value' => json_encode(['en' => 'Account Settings', 'kh' => 'ការកំណត់គណនី']), 'platform' => 'MOBILE'],
        ];

        foreach ($translations as $translation) {
            Translation::create($translation);
        }
    }
}
