<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str; // Required for Str::uuid()
use Carbon\Carbon; // Required for date manipulation

class BannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure the table is empty before seeding, or use conditional logic
        // DB::table('banners')->truncate(); // Optional: Clears the table first

        $now = Carbon::now();

        DB::table('banners')->insert([
            [
                'id' => Str::uuid()->toString(),
                'name' => 'Monsoon Health Pack Discount',
                'image_url_mobile' => 'https://placehold.co/750x400/E9D5FF/4A044E?text=Monsoon+Health+Pack',
                'image_url_tablet' => 'https://placehold.co/1200x600/E9D5FF/4A044E?text=Monsoon+Health+Pack',
                'title_text' => 'Stay Healthy This Monsoon!',
                'subtitle_text' => 'Get 20% off on our special monsoon health pack. Limited time offer!',
                'cta_text' => 'Order Medicines',
                'cta_action_type' => 'DEEP_LINK',
                'cta_action_value' => '/products/category/monsoon-health',
                'priority' => 10,
                'status' => 'ACTIVE',
                'start_date' => $now->copy()->subDays(5),
                'end_date' => $now->copy()->addDays(25),
                'display_locations' => 'HOME_SCREEN,MEDICINE_SECTION',
                'language_code' => 'en',
                'region_code' => 'GLOBAL',
                'impression_count' => 1500,
                'click_count' => 120,
                'created_by' => null, // Or specify a UUID if you have a users table seeded
                'updated_by' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => Str::uuid()->toString(),
                'name' => 'Find Nearby Hospitals Easily',
                'image_url_mobile' => 'https://placehold.co/750x400/D1FAE5/065F46?text=Nearby+Hospitals',
                'image_url_tablet' => 'https://placehold.co/1200x600/D1FAE5/065F46?text=Nearby+Hospitals',
                'title_text' => 'Emergency? Find Hospitals Fast!',
                'subtitle_text' => 'Locate top-rated hospitals and clinics near you with just one tap.',
                'cta_text' => 'Find Hospitals',
                'cta_action_type' => 'SERVICE_CATEGORY',
                'cta_action_value' => 'HOSPITAL_FINDER',
                'priority' => 8,
                'status' => 'ACTIVE',
                'start_date' => $now->copy()->subDays(10),
                'end_date' => $now->copy()->addYears(5), // For ongoing promotions, set a distant future date
                'display_locations' => 'HOME_SCREEN,HOSPITAL_SECTION',
                'language_code' => 'en',
                'region_code' => 'GLOBAL',
                'impression_count' => 2200,
                'click_count' => 90,
                'created_by' => null,
                'updated_by' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => Str::uuid()->toString(),
                'name' => 'Book Doctor Appointment Online',
                'image_url_mobile' => 'https://placehold.co/750x400/DBEAFE/1E40AF?text=Book+Appointment',
                'image_url_tablet' => 'https://placehold.co/1200x600/DBEAFE/1E40AF?text=Book+Appointment',
                'title_text' => 'Consult with Top Doctors',
                'subtitle_text' => 'Skip the queue. Book video consultations or clinic appointments easily.',
                'cta_text' => 'Book Now',
                'cta_action_type' => 'DEEP_LINK',
                'cta_action_value' => '/doctors/search',
                'priority' => 9,
                'status' => 'ACTIVE',
                'start_date' => $now,
                'end_date' => $now->copy()->addMonths(2),
                'display_locations' => 'HOME_SCREEN,APPOINTMENTS_SECTION',
                'language_code' => 'en',
                'region_code' => 'GLOBAL',
                'impression_count' => 1800,
                'click_count' => 150,
                'created_by' => null,
                'updated_by' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => Str::uuid()->toString(),
                'name' => 'Weekend Pharmacy Deals',
                'image_url_mobile' => 'https://placehold.co/750x400/FEF3C7/92400E?text=Weekend+Deals',
                'image_url_tablet' => null, // Example: No tablet-specific image
                'title_text' => 'Weekend Pharmacy Bonanza!',
                'subtitle_text' => 'Special discounts on essential medicines every weekend.',
                'cta_text' => 'Shop Deals',
                'cta_action_type' => 'EXTERNAL_URL',
                'cta_action_value' => 'https://yourpharmacy.example.com/weekend-deals',
                'priority' => 7,
                'status' => 'SCHEDULED', // Will become active based on start_date
                'start_date' => $now->copy()->next(Carbon::FRIDAY)->setTime(17, 0, 0), // Starts this coming Friday 5 PM
                'end_date' => $now->copy()->next(Carbon::SUNDAY)->setTime(23, 59, 59),   // Ends this coming Sunday night
                'display_locations' => 'MEDICINE_SECTION',
                'language_code' => 'en',
                'region_code' => 'US', // Example: Region specific
                'impression_count' => 0,
                'click_count' => 0,
                'created_by' => null,
                'updated_by' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
             [
                'id' => Str::uuid()->toString(),
                'name' => 'Dr. Emily Carter - Cardiologist',
                'image_url_mobile' => 'https://placehold.co/750x400/FCE7F3/831843?text=Dr.+Emily+Carter',
                'image_url_tablet' => 'https://placehold.co/1200x600/FCE7F3/831843?text=Dr.+Emily+Carter',
                'title_text' => 'Expert Heart Care',
                'subtitle_text' => 'Consult with Dr. Emily Carter, leading cardiologist. Book your slot today.',
                'cta_text' => 'View Profile',
                'cta_action_type' => 'DOCTOR_PROFILE',
                'cta_action_value' => 'doc_profile_uuid_emily_carter', // Replace with actual doctor profile ID/UUID
                'priority' => 6,
                'status' => 'ACTIVE',
                'start_date' => $now->copy()->subDays(1),
                'end_date' =>  $now->copy()->subDays(2), // This date is before the start_date and in the past
                'display_locations' => 'HOME_SCREEN,DOCTORS_SECTION',
                'language_code' => 'en',
                'region_code' => 'UK',
                'impression_count' => 950,
                'click_count' => 75,
                'created_by' => null,
                'updated_by' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        ]);
    }
}