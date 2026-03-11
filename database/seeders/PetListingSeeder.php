<?php

namespace Database\Seeders;

use App\Models\Pet;
use App\Models\PetListing;
use App\Models\User;
use Illuminate\Database\Seeder;

class PetListingSeeder extends Seeder
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

        $pets = Pet::all();

        foreach ($pets as $pet) {
            PetListing::updateOrCreate(
                ['pet_id' => $pet->id],
                [
                    'user_id' => $user->id,
                    'listing_type' => $pet->species === 'Dog' ? 'SALE' : 'ADOPTION',
                    'price' => $pet->species === 'Dog' ? 450.00 : 0.00,
                    'description' => "Available for " . ($pet->species === 'Dog' ? "Sale" : "Adoption") . ". " . $pet->medical_notes,
                    'status' => 'AVAILABLE',
                ]
            );
        }
    }
}
