<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str; // For UUID generation if not using model factory with auto-UUID
use App\Models\Hospital; // Assuming your Hospital model is in App\Models and handles UUIDs

class HospitalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // Array of sample hospitals in Cambodia
        // You should replace this with actual or more comprehensive data
        $hospitals = [
            [
                'name' => 'Calmette Hospital',
                'address' => 'Monivong Boulevard, Sangkat Srah Chork, Khan Daun Penh',
                'city' => 'Phnom Penh',
                'state' => 'Phnom Penh',
                'zip_code' => '12207',
                'country' => 'Cambodia',
                'phone_number' => '+855 23 426 948',
                'email' => 'info@calmette.gov.kh',
                'website' => 'http://www.calmette.gov.kh',
                'description' => 'A leading public hospital in Phnom Penh, offering a wide range of medical services.',
                'status' => 'ACTIVE',
                'created_at' => now(),
                'updated_at' => now(),
                // For created_by, updated_by, deleted_by, you might want to set a default user ID
                // or leave them as null if they are truly optional as per the migration.
                // If you have a default system user ID, you can use it here.
                // For simplicity, I'm setting them to a placeholder or null.
                'created_by' => null, // Or null, or a specific user ID string
                'updated_by' => null,
                'deleted_by' => null,
            ],
            [
                'name' => 'Royal Phnom Penh Hospital',
                'address' => 'No. 888, Russian Confederation Blvd, Sangkat Toeuk Thla, Khan Sen Sok',
                'city' => 'Phnom Penh',
                'state' => 'Phnom Penh',
                'zip_code' => '12102',
                'country' => 'Cambodia',
                'phone_number' => '+855 23 991 000',
                'email' => 'contact@royalrph.com',
                'website' => 'https://www.royalphnompenhhospital.com',
                'description' => 'A private hospital providing international standard healthcare services.',
                'status' => 'ACTIVE',
                'created_at' => now(),
                'updated_at' => now(),
                'created_by' => null,
                'updated_by' => null,
                'deleted_by' => null,
            ],
            [
                'name' => 'Angkor Hospital for Children',
                'address' => 'Tep Vong Road & Oum Chhay Street, Svay Dangkum',
                'city' => 'Siem Reap',
                'state' => 'Siem Reap Province',
                'zip_code' => '17252', // Example ZIP, verify actual
                'country' => 'Cambodia',
                'phone_number' => '+855 63 963 409',
                'email' => 'info@angkorhospital.org',
                'website' => 'http://angkorhospital.org',
                'description' => 'A non-profit paediatric healthcare organisation providing free, quality care to Cambodian children.',
                'status' => 'ACTIVE',
                'created_at' => now(),
                'updated_at' => now(),
                'created_by' => null,
                'updated_by' => null,
                'deleted_by' => null,
            ],
            [
                'name' => 'Khmer-Soviet Friendship Hospital',
                'address' => 'Yothapol Khemarak Phoumin Blvd (271), Sangkat Tumnob Tuek, Khan Chamkarmon',
                'city' => 'Phnom Penh',
                'state' => 'Phnom Penh',
                'zip_code' => '12306', // Example ZIP, verify actual
                'country' => 'Cambodia',
                'phone_number' => '+855 23 217 891',
                'email' => 'info@ksfh.gov.kh', // Fictional email
                'website' => null, // No official website readily available
                'description' => 'A large public hospital in Phnom Penh, also known as Preah Kossamak Hospital.',
                'status' => 'ACTIVE',
                'created_at' => now(),
                'updated_at' => now(),
                'created_by' => null,
                'updated_by' => null,
                'deleted_by' => null,
            ],
            [
                'name' => 'Sihanouk Hospital Center of HOPE',
                'address' => 'St. 134, Veal Vong Village, Sangkat Veal Vong, Khan 7 Makara',
                'city' => 'Phnom Penh',
                'state' => 'Phnom Penh',
                'zip_code' => '12253', // Example ZIP, verify actual
                'country' => 'Cambodia',
                'phone_number' => '+855 23 884 901',
                'email' => 'info@sihosp.org',
                'website' => 'http://sihosp.org',
                'description' => 'Provides free medical care to poor and disadvantaged Cambodians.',
                'status' => 'ACTIVE',
                'created_at' => now(),
                'updated_at' => now(),
                'created_by' => null,
                'updated_by' => null,
                'deleted_by' => null,
            ],
        ];

        // Insert data into the hospitals table
        // Using DB facade for direct insertion, or you can use the Hospital model
        foreach ($hospitals as $hospitalData) {
            // Generate UUID if not using model factory that handles it automatically
            // If your Hospital model's 'creating' event handles UUID generation, this line is not needed.
            if (!isset($hospitalData['id'])) {
                $hospitalData['id'] = (string) Str::uuid();
            }

            DB::table('hospitals')->insert($hospitalData);

            // Alternatively, if using the Hospital model and it's set up for mass assignment and UUIDs:
            // Hospital::create($hospitalData);
        }

        // You can also use a factory if you have one defined for Hospital
        // \App\Models\Hospital::factory()->count(10)->create(['country' => 'Cambodia']);
    }
}
