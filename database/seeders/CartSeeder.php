<?php

namespace Database\Seeders;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CartSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Retrieve existing users and products
        $users = User::all();
        $products = Product::all();

        if ($users->isEmpty() || $products->isEmpty()) {
            return;
        }

        foreach ($users as $user) {
            // Give 50% of users a cart
            if (rand(0, 1) === 0) {
                continue;
            }

            // Create a cart for user
            $cart = Cart::updateOrCreate(
                ['user_id' => $user->id, 'status' => 'ACTIVE'],
                [] // No other fields to update in carts table for now
            );

            // Give each cart a random number of products (between 1 and 4)
            $numberOfItems = rand(1, 4);
            $randomProducts = $products->random($numberOfItems);

            foreach ($randomProducts as $product) {
                CartItem::updateOrCreate(
                    ['cart_id' => $cart->id, 'itemable_id' => $product->id, 'itemable_type' => Product::class],
                    [
                        'quantity' => rand(1, 3),
                    ]
                );
            }
        }
    }
}
