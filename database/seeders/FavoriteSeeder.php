<?php

namespace Database\Seeders;

use App\Models\Favorite;
use App\Models\Product;
use App\Models\Pet;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class FavoriteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::all();
        $products = Product::all();
        $pets = Pet::all();

        if ($users->isEmpty()) {
            return;
        }

        foreach ($users as $user) {
            // Favorite some products
            if (!$products->isEmpty()) {
                $favProducts = $products->random(rand(1, 3));
                foreach ($favProducts as $product) {
                    Favorite::updateOrCreate(
                        [
                            'user_id' => $user->id,
                            'favorable_id' => $product->id,
                            'favorable_type' => Product::class
                        ],
                        ['id' => Str::uuid()]
                    );
                }
            }

            // Favorite some pets
            if (!$pets->isEmpty()) {
                $favPets = $pets->random(rand(1, 2));
                foreach ($favPets as $pet) {
                    Favorite::updateOrCreate(
                        [
                            'user_id' => $user->id,
                            'favorable_id' => $pet->id,
                            'favorable_type' => Pet::class
                        ],
                        ['id' => Str::uuid()]
                    );
                }
            }
        }
    }
}
