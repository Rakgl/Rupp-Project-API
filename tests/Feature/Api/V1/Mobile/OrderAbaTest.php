<?php

namespace Tests\Feature\Api\V1\Mobile;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\Store;
use App\Models\StoreInventory;
use App\Models\User;
use App\Services\PayWayService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class OrderAbaTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_creation_with_aba_payment_info()
    {
        // 1. Setup Data
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '012345678'
        ]);

        $store = Store::create([
            'id' => Str::uuid(),
            'name' => 'Main Store',
            'email' => 'store@example.com',
            'phone' => '012345678',
            'address' => 'Phnom Penh',
            'city' => 'Phnom Penh',
            'zip_code' => '12000',
            'status' => 'ACTIVE'
        ]);

        $category = Category::create([
            'id' => Str::uuid(),
            'name' => ['en' => 'Food'],
            'slug' => 'food',
            'status' => 'ACTIVE'
        ]);

        $product = Product::create([
            'id' => Str::uuid(),
            'category_id' => $category->id,
            'name' => ['en' => 'Test Product'],
            'slug' => 'test-product',
            'price' => 10.50,
            'status' => 'ACTIVE'
        ]);

        StoreInventory::create([
            'id' => Str::uuid(),
            'store_id' => $store->id,
            'product_id' => $product->id,
            'stock_quantity' => 100
        ]);

        $paymentMethod = PaymentMethod::create([
            'id' => Str::uuid(),
            'name' => 'KHQR',
            'type' => 'BANK',
            'status' => 'ACTIVE'
        ]);

        $cart = Cart::create([
            'id' => Str::uuid(),
            'user_id' => $user->id,
            'status' => 'ACTIVE'
        ]);

        CartItem::create([
            'id' => Str::uuid(),
            'cart_id' => $cart->id,
            'itemable_id' => $product->id,
            'itemable_type' => Product::class,
            'quantity' => 1
        ]);

        // 2. Mock PayWayService
        $this->mock(PayWayService::class, function ($mock) {
            $mock->shouldReceive('purchase')
                ->once()
                ->andReturn([
                    'success' => true,
                    'data' => [
                        'transaction_no' => 'ORD-12345678',
                        'qr_string' => 'TEST_QR_STRING',
                        'abapay_deeplink' => 'abapay://test'
                    ]
                ]);
        });

        // 3. Authenticate and perform request
        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/mobile/orders', [
                'fulfillment_type' => 'DELIVERY',
                'delivery_address' => 'Phnom Penh, Test Street',
                'payment_method_id' => $paymentMethod->id,
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('payment_info.qr_string', 'TEST_QR_STRING')
            ->assertJsonPath('data.total_amount', 12.50) // 10.50 + 2.00 delivery
            ->assertJsonPath('data.payment_status', 'UNPAID');

        // Check DB
        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'total_amount' => 12.50,
            'delivery_fee' => 2.00,
            'status' => 'PENDING'
        ]);

        // Check inventory was decremented
        $this->assertDatabaseHas('store_inventory', [
            'product_id' => $product->id,
            'stock_quantity' => 99
        ]);
        
        // Check cart items were deleted
        $this->assertEquals(0, DB::table('cart_items')->where('cart_id', $cart->id)->count());
    }

    public function test_payment_verification_with_aba_success()
    {
        $user = User::factory()->create();
        
        $paymentMethod = PaymentMethod::create([
            'id' => Str::uuid(),
            'name' => 'KHQR',
            'type' => 'BANK',
            'status' => 'ACTIVE'
        ]);

        $store = Store::create([
            'id' => Str::uuid(),
            'name' => 'Main Store',
            'address' => 'Phnom Penh',
            'city' => 'Phnom Penh',
            'zip_code' => '12000',
            'status' => 'ACTIVE'
        ]);

        $order = \App\Models\Order::create([
            'id' => Str::uuid(),
            'user_id' => $user->id,
            'store_id' => $store->id,
            'payment_method_id' => $paymentMethod->id,
            'order_number' => 'ORD-TEST-VERIFY',
            'subtotal' => 10.00,
            'total_amount' => 10.00,
            'status' => 'PENDING',
            'payment_status' => 'UNPAID',
        ]);

        // Mock PayWayService checkTransaction
        $this->mock(PayWayService::class, function ($mock) {
            $mock->shouldReceive('checkTransaction')
                ->once()
                ->andReturn([
                    'status' => [
                        'code' => '00',
                        'message' => 'Success'
                    ]
                ]);
        });

        $response = $this->actingAs($user, 'sanctum')
            ->postJson("/api/v1/mobile/orders/{$order->id}/verify-payment");

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.payment_status', 'PAID')
            ->assertJsonPath('data.status', 'PROCESSING');

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'payment_status' => 'PAID',
            'status' => 'PROCESSING'
        ]);
    }
}
