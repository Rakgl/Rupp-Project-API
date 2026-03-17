<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Store;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\StoreInventory;
use App\Services\PayWayService;
use App\Http\Resources\Api\V1\Mobile\Order\OrderResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    protected $payway;

    public function __construct(PayWayService $payway)
    {
        $this->payway = $payway;
    }
    /**
     * Display a listing of the user's orders.
     */
    public function index()
    {
        $orders = Order::where('user_id', Auth::id())
            ->with('orderItems.itemable')
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

            // Check stock only for Products
            foreach ($cart->items as $item) {
                if ($item->itemable_type === Product::class) {
                    $inventory = StoreInventory::where('product_id', $item->itemable_id)->first();
                    if (!$inventory || $inventory->stock_quantity < $item->quantity) {
                        return response()->json([
                            'success' => false,
                            'message' => "Insufficient stock for product: " . ($item->itemable->name[app()->getLocale()] ?? $item->itemable->name['en'] ?? 'Product')
                        ], 422);
                    }
                }
            }

            // Calculate totals
            $subtotal = $cart->items->sum(function ($item) {
                return $item->quantity * ($item->itemable->price ?? 0);
            });

            $deliveryFee = $request->fulfillment_type === 'DELIVERY' ? 2.00 : 0.00;
            $totalAmount = $subtotal + $deliveryFee;

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
                'tax_amount' => 0.00,
                'delivery_fee' => $deliveryFee,
                'total_amount' => $totalAmount,
                'fulfillment_type' => $request->fulfillment_type,
                'status' => 'PENDING',
                'payment_status' => 'UNPAID',
                'delivery_address' => $request->delivery_address,
            ]);

            // Create Order Items and decrement stock if Product
            foreach ($cart->items as $item) {
                OrderItem::create([
                    'id' => Str::uuid(),
                    'order_id' => $order->id,
                    'itemable_id' => $item->itemable_id,
                    'itemable_type' => $item->itemable_type,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->itemable->price,
                    'subtotal' => $item->quantity * $item->itemable->price,
                ]);

                if ($item->itemable_type === Product::class) {
                    StoreInventory::where('product_id', $item->itemable_id)
                        ->decrement('stock_quantity', $item->quantity);
                }
            }

            // Clear Cart
            $cart->items()->delete();
            $cart->update(['status' => 'CONVERTED']);

            // Load required relationships for ABA payment
            $order->load(['paymentMethod', 'orderItems.itemable']);

            // ABA Payment Integration
            $paymentInfo = null;
            if ($order->paymentMethod && $order->paymentMethod->name === 'KHQR') {
                $abaItems = $order->orderItems->map(function ($item) {
                    return [
                        'name' => is_array($item->itemable->name) ? ($item->itemable->name[app()->getLocale()] ?? $item->itemable->name['en'] ?? 'Item') : ($item->itemable->name ?? 'Item'),
                        'quantity' => (string) $item->quantity,
                        'price' => number_format($item->unit_price, 2, '.', ''),
                    ];
                })->toArray();

                $abaResponse = $this->payway->purchase($order->order_number, (float) $order->total_amount, [
                    'firstname' => $user->name ?? 'Guest',
                    'lastname' => '',
                    'email' => $user->email ?? 'guest@example.com',
                    'phone' => $user->phone ?? '012345678',
                    'items' => $abaItems,
                    'shipping' => number_format($order->delivery_fee, 2, '.', ''),
                ]);

                if ($abaResponse['success']) {
                    $paymentInfo = $abaResponse['data'];
                }
            }

            DB::commit();

            $order->load(['orderItems.itemable', 'paymentMethod']);

            $additional = [
                'success' => true,
                'message' => 'Order placed successfully.'
            ];

            if ($paymentInfo) {
                $additional['payment_info'] = $paymentInfo;
            }

            return (new OrderResource($order))->additional($additional);

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

        $order->load('orderItems.itemable');
        return new OrderResource($order);
    }

    /**
     * Verify payment status of an order.
     */
    public function verifyPayment(Request $request, Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($order->payment_status === 'PAID') {
            return response()->json(['message' => 'Order is already paid.'], 422);
        }

        // Verify with ABA PayWay if it was KHQR
        $order->load('paymentMethod');
        if ($order->paymentMethod && $order->paymentMethod->name === 'KHQR') {
            $response = $this->payway->checkTransaction($order->order_number);
            
            if (isset($response['status']['code']) && $response['status']['code'] === "00") {
                $order->update([
                    'payment_status' => 'PAID',
                    'status' => 'PROCESSING'
                ]);

                return (new OrderResource($order))->additional([
                    'success' => true,
                    'message' => 'Payment verified successfully.'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Payment not found or not completed yet.',
                'aba_response' => $response
            ], 422);
        }

        // For other payment methods or manual bypass if needed
        $order->update([
            'payment_status' => 'PAID',
            'status' => 'PROCESSING'
        ]);

        return (new OrderResource($order))->additional([
            'success' => true,
            'message' => 'Payment status updated.'
        ]);
    }

    /**
     * Cancel an order.
     */
    public function cancel(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($order->status !== 'PENDING') {
            return response()->json([
                'success' => false,
                'message' => 'Only pending orders can be cancelled.'
            ], 422);
        }

        try {
            DB::beginTransaction();

            $order->update(['status' => 'CANCELLED']);

            // Restore stock only for Products
            foreach ($order->orderItems as $item) {
                if ($item->itemable_type === Product::class) {
                    StoreInventory::where('product_id', $item->itemable_id)
                        ->increment('stock_quantity', $item->quantity);
                }
            }

            DB::commit();

            return (new OrderResource($order))->additional([
                'success' => true,
                'message' => 'Order cancelled successfully.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Could not cancel order.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
