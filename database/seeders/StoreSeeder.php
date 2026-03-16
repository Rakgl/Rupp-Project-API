<?php

namespace Database\Seeders;

use App\Models\Store;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class StoreSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('en_US');

        $specificStores = [
            [
                'name' => 'Paws & Whiskers Pet Shop',
                'address' => '#12, St. 271, Sangkat Boeung Tumpun',
                'city' => 'Phnom Penh',
                'state' => 'Phnom Penh',
                'zip_code' => '12000',
                'country' => 'Cambodia',
                'phone_number' => '+855 23 111 222',
                'telegram' => '@paws_whiskers_pp',
                'email' => 'info@pawswhiskers.com',
                'license_number' => 'PET-'.$faker->numerify('#####'),
                'opening_time' => '08:00:00',
                'closing_time' => '20:00:00',
                'is_24_hours' => false,
                'delivers_product' => true,
                'delivery_details' => 'Free delivery for orders over $20 within Phnom Penh.',
                'average_rating' => 4.8,
                'review_count' => 120,
                'is_verified' => true,
                'is_highlighted' => true,
                'is_top_choice' => true,
                'status' => 'ACTIVE',
            ],
            [
                'name' => 'Royal Pet Hospital & Clinic',
                'address' => 'No. 45, Mao Tse Toung Blvd',
                'city' => 'Phnom Penh',
                'state' => 'Phnom Penh',
                'zip_code' => '12000',
                'country' => 'Cambodia',
                'phone_number' => '+855 23 333 444',
                'telegram' => '@royalpethospital',
                'email' => 'contact@royalpet.com',
                'license_number' => 'VET-'.$faker->numerify('#####'),
                'opening_time' => '00:00:00',
                'closing_time' => '23:59:59',
                'is_24_hours' => true,
                'delivers_product' => false,
                'delivery_details' => null,
                'average_rating' => 4.9,
                'review_count' => 850,
                'is_verified' => true,
                'is_highlighted' => false,
                'is_top_choice' => true,
                'status' => 'ACTIVE',
            ],
            [
                'name' => 'Siem Reap Pet Care Center',
                'address' => 'National Road 6, Near Old Market',
                'city' => 'Siem Reap',
                'state' => 'Siem Reap',
                'zip_code' => '17000',
                'country' => 'Cambodia',
                'phone_number' => '+855 63 555 666',
                'telegram' => '@srpetcare',
                'email' => 'sr@petcare.com',
                'license_number' => 'PET-SR-'.$faker->numerify('#####'),
                'opening_time' => '08:30:00',
                'closing_time' => '19:00:00',
                'is_24_hours' => false,
                'delivers_product' => true,
                'delivery_details' => 'Local delivery in Siem Reap town.',
                'average_rating' => 4.6,
                'review_count' => 210,
                'is_verified' => true,
                'is_highlighted' => false,
                'is_top_choice' => false,
                'status' => 'ACTIVE',
            ],
            [
                'name' => 'Happy Tails Grooming & Spa',
                'address' => 'St. 63, Sangkat Tonle Bassac',
                'city' => 'Phnom Penh',
                'state' => 'Phnom Penh',
                'zip_code' => '12000',
                'country' => 'Cambodia',
                'phone_number' => '+855 23 777 888',
                'telegram' => '@happytails_spa',
                'email' => 'spa@happytails.com',
                'license_number' => 'GRM-'.$faker->numerify('#####'),
                'opening_time' => '09:00:00',
                'closing_time' => '18:00:00',
                'is_24_hours' => false,
                'delivers_product' => true,
                'delivery_details' => 'Pick up and drop off service available for grooming.',
                'average_rating' => 4.7,
                'review_count' => 340,
                'is_verified' => true,
                'is_highlighted' => true,
                'is_top_choice' => false,
                'status' => 'ACTIVE',
            ],
            [
                'name' => 'The Fish & Bird Emporium',
                'address' => 'Russian Federation Blvd, Sangkat Kakab',
                'city' => 'Phnom Penh',
                'state' => 'Phnom Penh',
                'zip_code' => '12000',
                'country' => 'Cambodia',
                'phone_number' => '+855 23 999 000',
                'telegram' => '@fishbird_emporium',
                'email' => 'fishbird@emporium.com',
                'license_number' => 'PET-'.$faker->numerify('#####'),
                'opening_time' => '08:00:00',
                'closing_time' => '19:00:00',
                'is_24_hours' => false,
                'delivers_product' => true,
                'delivery_details' => 'Specialized transport for live animals.',
                'average_rating' => 4.5,
                'review_count' => 180,
                'is_verified' => true,
                'is_highlighted' => false,
                'is_top_choice' => false,
                'status' => 'ACTIVE',
            ],
        ];

        foreach ($specificStores as $storeData) {
            Store::updateOrCreate(['name' => $storeData['name']], $storeData);
        }

        $this->command->info('Pet store seeding completed!');
    }
}
