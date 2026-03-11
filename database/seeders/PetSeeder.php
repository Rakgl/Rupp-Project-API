<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Pet;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where('username', 'admin')->first() ?? User::first();
        
        if (!$user) {
            return;
        }

        $categories = Category::where('type', 'PET')->get();

        $pets = [
            [
                'category_slug' => 'dog',
                'name' => 'Goji',
                'species' => 'Dog',
                'breed' => 'Pug',
                'weight' => 8.5,
                'date_of_birth' => '2016-05-15',
                'medical_notes' => 'A very cute dog did not bite or having any aggression. All vaccinations up to date.',
                'image_url' => 'https://images.unsplash.com/photo-1517849845537-4d257902454a?auto=format&fit=crop&w=800&q=80',
            ],
            [
                'category_slug' => 'cat',
                'name' => 'Luna',
                'species' => 'Cat',
                'breed' => 'Snow Leopard Hybrid',
                'weight' => 4.2,
                'date_of_birth' => '2022-01-10',
                'medical_notes' => 'A beautiful snow leopard hybrid cat. Very playful.',
                'image_url' => 'https://images.unsplash.com/photo-1495360010541-f48722b34f7d?auto=format&fit=crop&w=800&q=80',
            ],
            [
                'category_slug' => 'dog',
                'name' => 'Ein',
                'species' => 'Dog',
                'breed' => 'Corgy',
                'weight' => 12.0,
                'date_of_birth' => '2023-03-20',
                'medical_notes' => 'Energetic and friendly Corgy. Loves walks.',
                'image_url' => 'https://images.unsplash.com/photo-1583511655857-d19b40a7a54e?auto=format&fit=crop&w=800&q=80',
            ],
            [
                'category_slug' => 'fish',
                'name' => 'Nemo',
                'species' => 'Fish',
                'breed' => 'Gold fish',
                'weight' => 0.1,
                'date_of_birth' => '2023-11-01',
                'medical_notes' => 'Classic and beautiful gold fish.',
                'image_url' => 'https://images.unsplash.com/photo-1524704796725-9fc3044a58b2?auto=format&fit=crop&w=800&q=80',
            ]
        ];

        foreach ($pets as $petData) {
            $cat = $categories->where('slug', $petData['category_slug'])->first();
            
            Pet::updateOrCreate(
                ['name' => $petData['name'], 'user_id' => $user->id],
                [
                    'category_id' => $cat ? $cat->id : null,
                    'species' => $petData['species'],
                    'breed' => $petData['breed'],
                    'weight' => $petData['weight'],
                    'date_of_birth' => $petData['date_of_birth'],
                    'medical_notes' => $petData['medical_notes'],
                    'image_url' => $petData['image_url'],
                ]
            );
        }
    }
}
