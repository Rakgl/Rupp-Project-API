<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class StoreSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('en_US');

        // --- Create a few specific, hand-crafted car dealerships ---
        $this->command->info('Seeding specific car dealerships...');
        $specificStores = [
            [
                'id' => Str::uuid(),
                'name' => 'Autorayider Flagship Showroom',
                'address' => '#29, Mao Tse Toung Blvd',
                'city' => 'Phnom Penh',
                'state' => 'Phnom Penh',
                'zip_code' => $faker->numerify('12###'),
                'country' => 'Cambodia',
                'phone_number' => '+855 23 222 911',
                'telegram' => '@autorayider_official',
                'email' => 'sales@autorayider.com',
                'license_number' => 'DLR-AR'.$faker->numerify('#####'),
                'opening_time' => '08:30:00',
                'closing_time' => '20:00:00',
                'is_24_hours' => false,
                'delivers_product' => true,
                'delivery_details' => 'Home delivery, registration assistance, and financing available.',
                'average_rating' => 4.7,
                'review_count' => 1540,
                'is_verified' => true,
                'is_highlighted' => true,
                'is_top_choice' => true,
                'status' => 'ACTIVE',
                'created_at' => now(),
                'updated_at' => now(),
                'created_by' => null,
                'updated_by' => null,
                'deleted_by' => null,
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Ming Auto City – Sen Sok',
                'address' => 'No. 116C, Preah Trasak Paem St. (63)',
                'city' => 'Phnom Penh',
                'state' => 'Phnom Penh',
                'zip_code' => $faker->numerify('12###'),
                'country' => 'Cambodia',
                'phone_number' => '+855 23 883 664',
                'telegram' => '@mingautocity',
                'email' => 'sales.sensok@mingauto.example',
                'license_number' => 'DLR-MA'.$faker->numerify('#####'),
                'opening_time' => '09:00:00',
                'closing_time' => '19:00:00',
                'is_24_hours' => false,
                'delivers_product' => true,
                'delivery_details' => 'Test drives by appointment; trade-in accepted; bank financing partnerships.',
                'average_rating' => 4.5,
                'review_count' => 980,
                'is_verified' => true,
                'is_highlighted' => false,
                'is_top_choice' => false,
                'status' => 'ACTIVE',
                'created_at' => now(),
                'updated_at' => now(),
                'created_by' => null,
                'updated_by' => null,
                'deleted_by' => null,
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Elite Motors – Siem Reap',
                'address' => 'National Road 6, Krong Siem Reap',
                'city' => 'Siem Reap',
                'state' => 'Siem Reap',
                'zip_code' => $faker->numerify('17###'),
                'country' => 'Cambodia',
                'phone_number' => '+855 63 430 205',
                'telegram' => '@elitemotors_sr',
                'email' => $faker->unique()->safeEmail,
                'license_number' => 'DLR-EMSR'.$faker->numerify('#####'),
                'opening_time' => '08:30:00',
                'closing_time' => '18:30:00',
                'is_24_hours' => false,
                'delivers_product' => true,
                'delivery_details' => 'Free delivery within province, after-sales service, and warranty support.',
                'average_rating' => 4.6,
                'review_count' => 720,
                'is_verified' => true,
                'is_highlighted' => false,
                'is_top_choice' => false,
                'status' => 'ACTIVE',
                'created_at' => now(),
                'updated_at' => now(),
                'created_by' => null,
                'updated_by' => null,
                'deleted_by' => null,
            ],
        ];

        DB::table('stores')->insert($specificStores);

        // --- Generate and seed up to 10 total car dealerships in chunks ---
        $this->command->info('Seeding generated car dealerships...');
        $targetTotal = 10; // total dealerships you want in DB
        $remainingToGenerate = max(0, $targetTotal - count($specificStores));
        $chunkSize = 10; // insert up to 10 at a time
        $stores = [];

        for ($i = 0; $i < $remainingToGenerate; $i++) {
            $brandWord = $faker->randomElement([
                'Prime', 'Diamond', 'AutoLux', 'Skyline', 'Grand', 'Royal', 'Velocity', 'Torque', 'Roadstar', 'Fusion'
            ]);
            $suffix = $faker->randomElement(['Motors', 'Auto', 'Automall', 'Cars', 'Showroom', 'Dealers']);
            $city = $faker->randomElement(['Phnom Penh', 'Siem Reap', 'Battambang', 'Sihanoukville', 'Kampot']);
            $state = $city; // keep state aligned with city for simplicity

            $stores[] = [
                'id' => Str::uuid(),
                'name' => "{$brandWord} {$suffix} – {$city}",
                'address' => $faker->streetAddress,
                'city' => $city,
                'state' => $state,
                'zip_code' => $faker->numerify('#####'),
                'country' => 'Cambodia',
                'phone_number' => $faker->e164PhoneNumber,
                'telegram' => '@' . Str::lower($brandWord) . Str::lower($suffix),
                'email' => $faker->unique()->safeEmail,
                'license_number' => 'DLR-' . Str::upper(Str::random(2)) . $faker->numerify('#####'),
                'opening_time' => '09:00:00',
                'closing_time' => '19:00:00',
                'is_24_hours' => false,
                'delivers_product' => $faker->boolean(60), // many dealers offer delivery/transport
                'is_highlighted' => $faker->boolean(15),
                'is_top_choice' => $faker->boolean(12),
                'is_verified' => $faker->boolean(70),
                'delivery_details' => $faker->randomElement([
                    'Registration assistance, bank financing, and warranty available.',
                    'Test drives by appointment; trade-in accepted.',
                    'Nationwide transport and after-sales service.',
                    'Home delivery and loan application support.',
                ]),
                'status' => 'ACTIVE',
                'average_rating' => $faker->randomFloat(1, 4.2, 4.9),
                'review_count' => $faker->numberBetween(100, 2500),
                'created_at' => now(),
                'updated_at' => now(),
                'created_by' => null,
                'updated_by' => null,
                'deleted_by' => null,
            ];

            if (count($stores) === $chunkSize) {
                DB::table('stores')->insert($stores);
                $this->command->info("Seeded {$chunkSize} generated dealerships...");
                $stores = [];
            }
        }

        if (!empty($stores)) {
            DB::table('stores')->insert($stores);
            $this->command->info('Seeded final '.count($stores).' generated dealerships...');
        }

        $this->command->info('Car dealership seeding completed!');
    }
}