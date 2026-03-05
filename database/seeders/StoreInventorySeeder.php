<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Store;
use App\Models\StoreInventory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class StoreInventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stores = Store::all();
        $products = Product::all();
        
        if ($stores->isEmpty() || $products->isEmpty()) {
            return;
        }

        foreach ($stores as $store) {
            foreach ($products as $product) {
                // Randomly Decide to stock this item and quantity
                if (rand(1, 100) > 30) {
                    StoreInventory::create([
                        'id' => (string) Str::uuid(),
                        'store_id' => $store->id,
                        'product_id' => $product->id,
                        'stock_quantity' => rand(5, 100)
                    ]);
                }
            }
        }
    }
}
