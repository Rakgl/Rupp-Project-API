<?php

namespace Database\Seeders;

use App\Models\Favorite;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class FavoriteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Truncate the favorites table before seeding
        // We use DB::table to truncate since it's cleaner and avoids foreign key issues depending on the DB
        DB::statement('TRUNCATE TABLE favorites CASCADE');

        // Retrieve existing users and products
        $users = User::all();
        $products = Product::all();

        // If no users or products, there's nothing to seed
        if ($users->isEmpty() || $products->isEmpty()) {
            $this->command->info('No users or products found. Skipped seeding favorites.');
            return;
        }

        $favoritesInserted = 0;

        foreach ($users as $user) {
            // Give each user a random number of favorite products (e.g., between 0 and 5)
            $numberOfFavorites = rand(0, min(5, $products->count()));

            if ($numberOfFavorites === 0) {
                continue;
            }

            // Pick random products for the user
            $randomProducts = $products->random($numberOfFavorites);

            foreach ($randomProducts as $product) {
                // To avoid duplicate exceptions on the unique constraint, check if it exists
                $exists = Favorite::where('user_id', $user->id)
                    ->where('product_id', $product->id)
                    ->exists();

                if (!$exists) {
                    Favorite::create([
                        'id'         => Str::uuid(),
                        'user_id'    => $user->id,
                        'product_id' => $product->id,
                    ]);
                    $favoritesInserted++;
                }
            }
        }

        $this->command->info("Successfully seeded {$favoritesInserted} favorites.");
    }
}
