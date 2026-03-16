<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Store;
use App\Models\PaymentMethod;
use App\Http\Resources\Api\V1\Mobile\Order\OrderResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    /**
     * Display a listing of the user's orders.
     */
    public function index()
    {
        $orders = Order::where('user_id', Auth::id())
            ->with('orderItems.product')
            ->latest()
            ->paginate(10);

        return OrderResource::collection($orders);
    }

    /**
     * Store a newly created order from the current cart.
     */
    public function store(Request $request)
    {
        $request->validate([
            'fulfillment_type' => 'required|in:PICKUP,DELIVERY',
            'delivery_address' => 'required_if:fulfillment_type,DELIVERY|string|max:500',
            'payment_method_id' => 'nullable|uuid|exists:payment_methods,id',
        ]);

        try {
            DB::beginTransaction();

            $user = Auth::user();
            $cart = Cart::where('user_id', $user->id)
                ->where('status', 'ACTIVE')
                ->with('items.itemable')
                ->first();

            if (!$cart || $cart->items->isEmpty()) {
                return response()->json(['message' => 'Your cart is empty'], 422);
            }

            // Calculate totals
            $subtotal = $cart->items->sum(function ($item) {
                return $item->quantity * ($item->itemable->price ?? 0);
            });

            $store = Store::first();
            $paymentMethod = $request->payment_method_id ?? PaymentMethod::first()?->id;

            // Create Order
            $order = Order::create([
                'id' => Str::uuid(),
                'user_id' => $user->id,
                'store_id' => $store?->id,
                'payment_method_id' => $paymentMethod,
                'order_number' => 'ORD-' . strtoupper(Str::random(8)),
                'subtotal' => $subtotal,
                'total_amount' => $subtotal, // Adding tax/delivery fee logic here later if needed
                'fulfillment_type' => $request->fulfillment_type,
                'status' => 'PENDING',
                'payment_status' => 'UNPAID',
                'delivery_address' => $request->delivery_address,
            ]);

            // Create Order Items
            foreach ($cart->items as $item) {
                // Only products can be in order for now based on OrderItem schema
                if ($item->itemable_type === \App\Models\Product::class) {
                    OrderItem::create([
                        'id' => Str::uuid(),
                        'order_id' => $order->id,
                        'product_id' => $item->itemable_id,
                        'quantity' => $item->quantity,
                        'unit_price' => $item->itemable->price,
                        'subtotal' => $item->quantity * $item->itemable->price,
                    ]);
                }
            }

            // Clear Cart
            $cart->items()->delete();
            $cart->update(['status' => 'CONVERTED']);

            DB::commit();

            $order->load('orderItems.product');

            return (new OrderResource($order))->additional([
                'success' => true,
                'message' => 'Order placed successfully.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Could not place order.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $order->load('orderItems.product');
        return new OrderResource($order);
    }
}
