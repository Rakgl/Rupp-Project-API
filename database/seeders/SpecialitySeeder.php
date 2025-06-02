<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Speciality; // Renamed from YourNewModel
use Faker\Factory as Faker;
use Illuminate\Support\Str;

class SpecialitySeeder extends Seeder // Renamed from YourNewModelSeeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // Example: Create 10 specialities
        for ($i = 0; $i < 10; $i++) {
            Speciality::create([ // Changed from YourNewModel
                'name' => $faker->unique()->jobTitle, // Using jobTitle for more relevant speciality names
                'image' => $faker->imageUrl(640, 480, 'medical'), // Placeholder image relevant to specialities
                'description' => $faker->sentence(10), // A bit longer description
                'status' => 'ACTIVE',
                'created_by' => null,
                'updated_by' => null,
                'deleted_by' => null, // Typically null for new, non-deleted records
            ]);
        }

        // Example of creating a specific speciality
        Speciality::create([ // Changed from YourNewModel
            'name' => 'Cardiology',
            'image' => '[https://placehold.co/600x400/EBF4FF/7F9CF5?text=Cardiology](https://placehold.co/600x400/EBF4FF/7F9CF5?text=Cardiology)',
            'description' => 'The branch of medicine that deals with diseases and abnormalities of the heart.',
            'status' => 'ACTIVE', // Or 'true' if matching the string default
            'created_by' => null,
            'updated_by' => null,
            'deleted_by' => null,
        ]);

        Speciality::create([ // Changed from YourNewModel
            'name' => 'Neurology',
            'image' => '[https://placehold.co/600x400/FFD700/000000?text=Neurology](https://placehold.co/600x400/FFD700/000000?text=Neurology)',
            'description' => 'The branch of medicine or biology that deals with the anatomy, functions, and organic disorders of nerves and the nervous system.',
            'status' => 'ACTIVE',
            'created_by' => null,
            'updated_by' => null,
            'deleted_by' => null,
        ]);

        Speciality::create([ // Changed from YourNewModel
            'name' => 'Pediatrics',
            'image' => '[https://placehold.co/600x400/ADD8E6/000000?text=Pediatrics](https://placehold.co/600x400/ADD8E6/000000?text=Pediatrics)',
            'description' => 'The branch of medicine dealing with children and their diseases.',
            'status' => 'ACTIVE', // Matching string default example
            'created_by' => null,
            'updated_by' => null,
            'deleted_by' => null,
        ]);
    }
}
