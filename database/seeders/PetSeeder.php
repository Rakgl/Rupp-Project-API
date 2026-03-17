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
        $users = User::all();
        
        if ($users->isEmpty()) {
            return;
        }

        $categories = Category::where('type', 'PET')->get();

        $pets = [
            [
                'category_slug' => 'dogs',
                'name' => 'Goji',
                'species' => 'Dog',
                'breed' => 'Pug',
                'weight' => 8.5,
                'date_of_birth' => '2016-05-15',
                'medical_notes' => 'A very cute dog. All vaccinations up to date.',
                'price' => 1000,
                'image_url' => 'https://images.unsplash.com/photo-1517849845537-4d257902454a?auto=format&fit=crop&w=800&q=80',
            ],
            [
                'category_slug' => 'cats',
                'name' => 'Luna',
                'species' => 'Cat',
                'breed' => 'Snow Leopard Hybrid',
                'weight' => 4.2,
                'date_of_birth' => '2022-01-10',
                'medical_notes' => 'A beautiful snow leopard hybrid cat. Very playful.',
                'price' => 2000,
                'image_url' => 'https://images.unsplash.com/photo-1495360010541-f48722b34f7d?auto=format&fit=crop&w=800&q=80',
            ],
            [
                'category_slug' => 'dogs',
                'name' => 'Ein',
                'species' => 'Dog',
                'breed' => 'Corgi',
                'weight' => 12.0,
                'date_of_birth' => '2023-03-20',
                'medical_notes' => 'Energetic and friendly Corgi. Loves walks.',
                'price' => 3000,
                'image_url' => 'https://images.unsplash.com/photo-1583511655857-d19b40a7a54e?auto=format&fit=crop&w=800&q=80',
            ],
            [
                'category_slug' => 'fish',
                'name' => 'Nemo',
                'species' => 'Fish',
                'breed' => 'Goldfish',
                'weight' => 0.1,
                'date_of_birth' => '2023-11-01',
                'medical_notes' => 'Classic and beautiful goldfish.',
                'price' => 10,
                'image_url' => 'https://images.unsplash.com/photo-1524704796725-9fc3044a58b2?auto=format&fit=crop&w=800&q=80',
            ],
            // [
            //     'category_slug' => 'birds',
            //     'name' => 'Sky',
            //     'species' => 'Bird',
            //     'breed' => 'Parrot',
            //     'weight' => 0.5,
            //     'date_of_birth' => '2023-05-10',
            //     'medical_notes' => 'Very talkative and healthy parrot.',
            //     'price' => 1500,
            //     'image_url' => 'https://images.unsplash.com/photo-1552728089-57bdde30eba3?auto=format&fit=crop&w=800&q=80',
            // ],
            // [
            //     'category_slug' => 'rabbits',
            //     'name' => 'Coco',
            //     'species' => 'Rabbit',
            //     'breed' => 'Holland Lop',
            //     'weight' => 1.8,
            //     'date_of_birth' => '2023-08-22',
            //     'medical_notes' => 'Gentle rabbit, loves carrots.',
            //     'price' => 500,
            //     'image_url' => 'https://images.unsplash.com/photo-1585110396054-c8182a7a8ce0?auto=format&fit=crop&w=800&q=80',
            // ],
            [
                'category_slug' => 'dogs',
                'name' => 'Max',
                'species' => 'Dog',
                'breed' => 'Golden Retriever',
                'weight' => 28.0,
                'date_of_birth' => '2021-02-14',
                'medical_notes' => 'Very friendly, good with kids.',
                'price' => 1200,
                'image_url' => 'https://images.unsplash.com/photo-1552053831-71594a27632d?auto=format&fit=crop&w=800&q=80',
            ],
            [
                'category_slug' => 'cats',
                'name' => 'Simba',
                'species' => 'Cat',
                'breed' => 'Maine Coon',
                'weight' => 7.5,
                'date_of_birth' => '2020-11-20',
                'medical_notes' => 'Large and majestic Maine Coon.',
                'price' => 2500,
                'image_url' => 'https://images.unsplash.com/photo-1533738363-b7f9aef128ce?auto=format&fit=crop&w=800&q=80',
            ],
        ];

        foreach ($pets as $petData) {
            $cat = $categories->where('slug', $petData['category_slug'])->first();
            $user = $users->random();
            
            Pet::updateOrCreate(
                ['name' => $petData['name'], 'user_id' => $user->id],
                [
                    'category_id' => $cat ? $cat->id : null,
                    'species' => $petData['species'],
                    'breed' => $petData['breed'],
                    'weight' => $petData['weight'],
                    'date_of_birth' => $petData['date_of_birth'],
                    'medical_notes' => $petData['medical_notes'],
                    'price' => $petData['price'],
                    'image_url' => $petData['image_url'],
                ]
            );
        }
    }
}
