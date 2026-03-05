<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\PaymentMethod;
use App\Models\Store;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stores = Store::all();
        $users = User::all();
        $paymentMethods = PaymentMethod::all();
        
        if ($stores->isEmpty() || $users->isEmpty() || $paymentMethods->isEmpty()) {
            // we should create a basic payment method if it doesn't exist
            if ($paymentMethods->isEmpty()) {
                $paymentMethod = PaymentMethod::create([
                    'id' => (string) Str::uuid(),
                    'name' => 'Cash',
                    'type' => 'cash',
                    'status' => 'ACTIVE'
                ]);
                $paymentMethods = collect([$paymentMethod]);
            } else {
                return; // cannot proceed smoothly
            }
        }

        $now = Carbon::now();

        // Create 20 random past orders
        for ($i = -10; $i <= 0; $i++) {
            $orderTime = $now->copy()->addDays($i)->subHours(rand(1, 10));
            $isToday = ($i == 0);

            Order::create([
                'id' => (string) Str::uuid(),
                'user_id' => $users->random()->id,
                'store_id' => $stores->random()->id,
                'payment_method_id' => $paymentMethods->random()->id,
                'order_number' => 'ORD-' . strtoupper(Str::random(8)),
                'total_amount' => rand(15, 200) + (rand(0, 99) / 100),
                'fulfillment_type' => collect(['PICKUP', 'DELIVERY'])->random(),
                // If it is today, we make sure to have at least a couple PENDING and COMPLETED
                'status' => $isToday ? collect(['PENDING', 'COMPLETED', 'PROCESSING'])->random() : 'COMPLETED',
                'payment_status' => collect(['PAID', 'UNPAID'])->random(),
                'delivery_address' => 'Sample Address',
                'created_at' => $orderTime,
                'updated_at' => $orderTime,
            ]);
        }

        // Force exactly two COMPLETED today for revenue KPI
        for ($i = 0; $i < 2; $i++) {
            Order::create([
                'id' => (string) Str::uuid(),
                'user_id' => $users->random()->id,
                'store_id' => $stores->random()->id,
                'payment_method_id' => $paymentMethods->random()->id,
                'order_number' => 'ORD-' . strtoupper(Str::random(8)),
                'total_amount' => 50.00,
                'fulfillment_type' => 'DELIVERY',
                'status' => 'COMPLETED',
                'payment_status' => 'PAID',
                'delivery_address' => 'Forced address',
                'created_at' => Carbon::today()->addHours(10),
                'updated_at' => Carbon::now(),
            ]);
        }

        // Force two PENDING orders
        for ($i = 0; $i < 2; $i++) {
            Order::create([
                'id' => (string) Str::uuid(),
                'user_id' => $users->random()->id,
                'store_id' => $stores->random()->id,
                'payment_method_id' => $paymentMethods->random()->id,
                'order_number' => 'ORD-' . strtoupper(Str::random(8)),
                'total_amount' => 30.00,
                'fulfillment_type' => 'PICKUP',
                'status' => 'PENDING',
                'payment_status' => 'UNPAID',
                'delivery_address' => null,
                'created_at' => Carbon::now()->subMinutes(15),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
