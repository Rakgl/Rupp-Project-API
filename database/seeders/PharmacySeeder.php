<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Pharmacy;
use Illuminate\Support\Facades\DB; // Required if using DB::table()
use Illuminate\Support\Str; // For UUID if not using HasUuids trait directly for seeding
use Faker\Factory as Faker; // Import Faker

class PharmacySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create(); // Initialize Faker

        Pharmacy::create([
            'name' => 'Ucare Pharmacy - Head Office',
            'address' => '#29, Mao Tse Toung Blvd',
            'city' => 'Phnom Penh',
            'state' => 'Phnom Penh',
            'zip_code' => $faker->numerify('12###'), // Example Phnom Penh zip
            'country' => 'Cambodia',
            'phone_number' => '+855 23 224 199',
            'email' => $faker->unique()->safeEmail, // 'info@ucarepharma.com' - make unique for seeder
            'license_number' => 'LIC-UC'.$faker->numerify('#####'),
            'opening_time' => '08:00:00',
            'closing_time' => '21:00:00',
            'is_24_hours' => false,
            'delivers_medication' => true,
            'delivery_details' => 'Multiple branches, delivery options vary. Check with local branch.',
            'status' => true,
            'created_by' => null,
            'updated_by' => null,
            'deleted_by' => null,
        ]);

        Pharmacy::create([
            'name' => 'Western Pharmacy - Trasak Paem',
            'address' => 'No. 116C, Preah Trasak Paem St. (63)',
            'city' => 'Phnom Penh',
            'state' => 'Phnom Penh',
            'zip_code' => $faker->numerify('12###'),
            'country' => 'Cambodia',
            'phone_number' => '023883664',
            'email' => $faker->unique()->safeEmail, // western.pharmacy.tp@example.com
            'license_number' => 'LIC-WP'.$faker->numerify('#####'),
            'opening_time' => '07:00:00',
            'closing_time' => '21:00:00',
            'is_24_hours' => false,
            'delivers_medication' => $faker->boolean(60),
            'delivery_details' => 'Offers free blood pressure and blood sugar screenings.',
            'status' => true,
            'created_by' => null,
            'updated_by' => null,
            'deleted_by' => null,
        ]);

        Pharmacy::create([
            'name' => 'Royal Pharmacy - St 199',
            'address' => 'No. 71Eo, St. 199',
            'city' => 'Phnom Penh',
            'state' => 'Phnom Penh',
            'zip_code' => '12309', // From search result
            'country' => 'Cambodia',
            'phone_number' => '012 803 600', // Masked last two digits in search
            'email' => $faker->unique()->safeEmail, // royal.pharmacy.199@example.com
            'license_number' => 'LIC-RP'.$faker->numerify('#####'),
            'opening_time' => '07:00:00',
            'closing_time' => '20:00:00',
            'is_24_hours' => false,
            'delivers_medication' => $faker->boolean(50),
            'delivery_details' => $faker->sentence,
            'status' => true,
            'created_by' => null,
            'updated_by' => null,
            'deleted_by' => null,
        ]);

        Pharmacy::create([
            'name' => 'University of Puthisastra Pharmacy',
            'address' => '#124, Street 184 Sangkat Boeung Raing',
            'city' => 'Phnom Penh',
            'state' => 'Phnom Penh',
            'zip_code' => $faker->numerify('12###'),
            'country' => 'Cambodia',
            'phone_number' => '+855 61 377 753',
            'email' => $faker->unique()->safeEmail, // up.pharmacy@example.com
            'license_number' => 'LIC-UPP'.$faker->numerify('#####'),
            'opening_time' => '08:00:00',
            'closing_time' => '20:30:00', // Mon-Fri
            'is_24_hours' => false, // Has 24/7 vending machines for non-prescription
            'delivers_medication' => true,
            'delivery_details' => 'Dispenses prescribed and OTC drugs. Offers health and beauty items, medical equipment.',
            'status' => true,
            'created_by' => null,
            'updated_by' => null,
            'deleted_by' => null,
        ]);

        Pharmacy::create([
            'name' => 'La Paix Pharmacy - Siem Reap',
            'address' => 'Sivatha (St.), Mondul I Village',
            'city' => 'Siem Reap',
            'state' => 'Siem Reap',
            'zip_code' => $faker->numerify('17###'), // Example Siem Reap zip
            'country' => 'Cambodia',
            'phone_number' => '092 684 600', // Masked last two digits
            'email' => 'lapaixpharmacy.sr@example.com', // Made up, ensure unique
            'license_number' => 'LIC-LPSR'.$faker->numerify('#####'),
            'opening_time' => '08:00:00',
            'closing_time' => '23:00:00',
            'is_24_hours' => false,
            'delivers_medication' => $faker->boolean(60),
            'delivery_details' => $faker->sentence,
            'status' => true,
            'created_by' => null,
            'updated_by' => null,
            'deleted_by' => null,
        ]);

         Pharmacy::create([
            'name' => 'Angkor Thom Pharmacy - Siem Reap',
            'address' => 'St.Sivatha Mondol Pir Village, S/K Svay Dangkum',
            'city' => 'Siem Reap',
            'state' => 'Siem Reap',
            'zip_code' => $faker->numerify('17###'),
            'country' => 'Cambodia',
            'phone_number' => '063 963 759',
            'email' => $faker->unique()->safeEmail, // angkorthom.pharmacy@example.com
            'license_number' => 'LIC-ATSR'.$faker->numerify('#####'),
            'opening_time' => '08:00:00',
            'closing_time' => '23:00:00',
            'is_24_hours' => false,
            'delivers_medication' => $faker->boolean(70),
            'delivery_details' => 'French-speaking staff available.',
            'status' => true,
            'created_by' => null,
            'updated_by' => null,
            'deleted_by' => null,
        ]);

        Pharmacy::create([
            'name' => 'Pharmacie de la Gare - Phnom Penh',
            'address' => '81 Monivong Boulevard',
            'city' => 'Phnom Penh',
            'state' => 'Phnom Penh',
            'zip_code' => $faker->numerify('12###'),
            'country' => 'Cambodia',
            'phone_number' => '+855 23 430 205',
            'email' => $faker->unique()->safeEmail, // pharmaciedelagare.pp@example.com
            'license_number' => 'LIC-PDGPP'.$faker->numerify('#####'),
            'opening_time' => $faker->randomElement(['08:00:00', '09:00:00', null]),
            'closing_time' => $faker->randomElement(['19:00:00', '20:00:00', null]),
            'is_24_hours' => $faker->boolean(5),
            'delivers_medication' => $faker->boolean(50),
            'delivery_details' => $faker->sentence,
            'status' => true,
            'created_by' => null,
            'updated_by' => null,
            'deleted_by' => null,
        ]);


        // --- End of Cambodian Pharmacies ---


        // Example of creating a specific pharmacy (already existed, kept for reference)
        Pharmacy::create([
            'name' => 'Ucare Pharmacy - BKK1 Branch (Example)', // Differentiated from Head Office
            'address' => 'Corner St 51 & St 278, Boeung Keng Kang 1',
            'city' => 'Phnom Penh',
            'state' => 'Phnom Penh',
            'zip_code' => '12302',
            'country' => 'Cambodia',
            'phone_number' => '+855 23 218 200',
            'email' => 'ucare.bkk1.branch@example.com', // Ensure unique email
            'license_number' => 'LIC-KH12345BR', // Differentiated license
            'opening_time' => '08:00:00',
            'closing_time' => '22:00:00',
            'is_24_hours' => false,
            'delivers_medication' => true,
            'delivery_details' => 'Delivery available in Phnom Penh city limits for BKK1 branch.',
            'status' => true,
            'created_by' => null,
            'updated_by' => null,
            'deleted_by' => null,
        ]);

    }
}

