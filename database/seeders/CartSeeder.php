<?php

namespace Database\Seeders;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class CartSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Truncate the carts and cart items table before seeding
        DB::statement('TRUNCATE TABLE carts CASCADE');
        DB::statement('TRUNCATE TABLE cart_items CASCADE');

        // Retrieve existing users and products
        $users = User::all();
        $products = Product::all();

        if ($users->isEmpty() || $products->isEmpty()) {
            $this->command->info('No users or products found. Skipped seeding carts.');
            return;
        }

        $cartsInserted = 0;
        $cartItemsInserted = 0;

        foreach ($users as $user) {
            // Give 50% of users a cart
            if (rand(0, 1) === 0) {
                continue;
            }

            // Create a cart for user
            $cart = Cart::create([
                'id'         => Str::uuid(),
                'user_id'    => $user->id,
                'session_id' => null,
                'status'     => 'ACTIVE',
            ]);
            $cartsInserted++;

            // Give each cart a random number of products (between 1 and 4)
            $numberOfItems = rand(1, min(4, $products->count()));
            $randomProducts = $products->random($numberOfItems);

            foreach ($randomProducts as $product) {
                CartItem::create([
                    'id'            => Str::uuid(),
                    'cart_id'       => $cart->id,
                    'itemable_id'   => $product->id,
                    'itemable_type' => Product::class,
                    'quantity'      => rand(1, 4), // random quantity
                ]);
                $cartItemsInserted++;
            }
        }

        $this->command->info("Successfully seeded {$cartsInserted} carts with {$cartItemsInserted} cart items.");
    }
}
