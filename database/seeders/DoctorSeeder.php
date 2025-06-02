<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Doctor;
use App\Models\User; // Assuming User model exists
use App\Models\Hospital; // Assuming Hospital model exists
use App\Models\Speciality; // Assuming Speciality model exists
use Faker\Factory as Faker;
use Illuminate\Support\Str; // For Str::uuid()

class DoctorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // Fetch existing User IDs, Hospital IDs, and Speciality IDs.
        // Ensure you have these seeded before running this seeder.
        $userIds = User::pluck('id')->toArray();
        $hospitalIds = Hospital::pluck('id')->toArray();
        $specialityIds = Speciality::pluck('id')->toArray();

        if (empty($userIds)) {
            $this->command->warn('No users found. Skipping DoctorSeeder or create users first.');
            return;
        }

        if (empty($specialityIds)) {
            $this->command->warn('No specialities found. Doctors will not be assigned specialities. Please seed specialities first.');
            // Optionally, you could decide to not create doctors if no specialities exist,
            // or allow doctors to be created without specialities.
            // For this example, we'll continue and doctors just won't have specialities if none exist.
        }

        // Determine a user ID for 'created_by', 'updated_by' fields in the pivot table
        // This could be a specific admin user ID, or a random user for variety.
        // For simplicity, let's pick one if available, otherwise use a placeholder string.
        $seederUserId = !empty($userIds) ? $faker->randomElement($userIds) : 'SeederSystemID';


        for ($i = 0; $i < 15; $i++) { // Create 15 sample doctors
            $user = User::find($faker->randomElement($userIds));
            if (!$user) continue;

            $qualifications = [];
            for ($q = 0; $q < $faker->numberBetween(1, 4); $q++) {
                $qualifications[] = $faker->randomElement(['MBBS', 'MD', 'PhD', 'FCPS', 'MRCP', 'FRCS']) . ' (' . $faker->company . ')';
            }

            $doctor = Doctor::create([
                'user_id' => $user->id,
                'title' => $faker->randomElement(['Dr.', 'Prof.', null]),
                'registration_number' => 'REG-' . Str::random(4) . $faker->unique()->numerify('#####'),
                'bio' => $faker->paragraph(3),
                'gender' => $faker->randomElement(['male', 'female', 'other']),
                'date_of_birth' => $faker->dateTimeBetween('-65 years', '-30 years')->format('Y-m-d'),
                'consultation_fee' => $faker->randomElement(['20', '30', '50', '75', '100']),
                'currency_code' => 'USD',
                'years_of_experience' => $faker->numberBetween(3, 35),
                'qualifications' => json_encode($qualifications),
                'profile_picture_path' => $faker->imageUrl(400, 400, 'people,doctor', true, 'Faker'),
                'is_verified' => $faker->boolean(75),
                'is_available_for_consultation' => $faker->boolean(80),
                'availability_status' => $faker->randomElement(['available', 'busy', 'offline']),
                'average_rating' => $faker->boolean(80) ? $faker->randomFloat(1, 3, 5) : null,
                'hospital_id' => !empty($hospitalIds) ? $faker->randomElement(array_merge($hospitalIds, [null])) : null,
                'status' => $faker->randomElement(['ACTIVE', 'INACTIVE', 'ON_LEAVE']),
                'created_by' => null,
                'updated_by' => null, // Or $user->id
                'deleted_by' => null,
            ]);

            // Attach specialities if available
            if (!empty($specialityIds)) {
                $numberOfSpecialities = $faker->numberBetween(1, min(3, count($specialityIds))); // Assign 1 to 3 specialities, or fewer if not enough available
                $selectedSpecialityIds = $faker->randomElements($specialityIds, $numberOfSpecialities);

                foreach ($selectedSpecialityIds as $specialityId) {
                    // Prepare pivot data, including a new UUID for the pivot table's 'id'
                    $pivotData = [
                        'id' => Str::uuid()->toString(), // Generate UUID for the pivot record
                        'created_by' => null, // Use a consistent user ID or logic for this
                        'updated_by' => null,
                        'deleted_by' => null,
                        // created_at and updated_at will be handled by withTimestamps()
                    ];
                    $doctor->specialities()->attach($specialityId, $pivotData);
                }
            }
        }

        // Example of a specific doctor
        if (!empty($userIds)) {
            $specificUser = User::find($faker->randomElement($userIds));
            if ($specificUser) {
                 $specificDoctor = Doctor::create([
                    'user_id' => $specificUser->id,
                    'title' => 'Dr.',
                    'registration_number' => 'REG-SPEC78901', // Ensure this is unique if you run seeder multiple times
                    'bio' => 'Highly experienced cardiologist with over 20 years in the field. Specializes in interventional cardiology and heart rhythm disorders.',
                    'gender' => 'female',
                    'date_of_birth' => '1975-08-15',
                    'consultation_fee' => '150',
                    'currency_code' => 'USD',
                    'years_of_experience' => 22,
                    'qualifications' => json_encode(['MD (Cardiology)', 'Fellow of American College of Cardiology (FACC)']),
                    'profile_picture_path' => '[https://placehold.co/400x400/EBF4FF/7F9CF5?text=Dr.JaneDoe](https://placehold.co/400x400/EBF4FF/7F9CF5?text=Dr.JaneDoe)',
                    'is_verified' => true,
                    'is_available_for_consultation' => true,
                    'availability_status' => 'available',
                    'average_rating' => 4.8,
                    'hospital_id' => !empty($hospitalIds) ? $faker->randomElement($hospitalIds) : null,
                    'status' => 'ACTIVE',
                    'created_by' => null,
                    'updated_by' => null,
                    'deleted_by' => null,
                ]);

                // Attach specific specialities to the specific doctor
                if (!empty($specialityIds)) {
                    $cardiologySpeciality = Speciality::where('name', 'Cardiology')->first(); // Example: find a specific speciality
                    if ($cardiologySpeciality) {
                        $specificDoctor->specialities()->attach($cardiologySpeciality->id, [
                            'id' => Str::uuid()->toString(),
                            'created_by' => null,
                            'updated_by' => null,
                            'deleted_by' => null,
                        ]);
                    }
                    // Attach another random one if desired
                    $anotherSpecialityId = $faker->randomElement($specialityIds);
                    if ($anotherSpecialityId && (!$cardiologySpeciality || $anotherSpecialityId !== $cardiologySpeciality->id)) {
                         $specificDoctor->specialities()->attach($anotherSpecialityId, [
                            'id' => Str::uuid()->toString(),
                            'created_by' => null,
                            'updated_by' => null,
                            'deleted_by' => null,
                        ]);
                    }
                }
            }
        }
    }
}