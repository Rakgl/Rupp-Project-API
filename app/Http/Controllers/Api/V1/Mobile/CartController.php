<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\Mobile\Cart\CartResource;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    /**
     * Get or create a cart for the current user/session.
     */
    private function getCart(Request $request): Cart
    {
        $user = $request->user();
        $sessionId = $request->header('X-Session-ID');

        if (!$user && !$sessionId) {
            // Generate a session ID if neither user nor session is provided
            $sessionId = Str::uuid()->toString();
        }

        $cart = null;

        if ($user) {
            $cart = Cart::where('user_id', $user->id)->where('status', 'ACTIVE')->first();
            
            if (!$cart) {
                $cart = Cart::create([
                    'id' => Str::uuid(),
                    'user_id' => $user->id,
                    'status' => 'ACTIVE'
                ]);
            }
        } else {
            $cart = Cart::where('session_id', $sessionId)->where('status', 'ACTIVE')->first();
            
            if (!$cart) {
                $cart = Cart::create([
                    'id' => Str::uuid(),
                    'session_id' => $sessionId,
                    'status' => 'ACTIVE'
                ]);
            }
        }

        return $cart;
    }

    /**
     * Retrieve the current active cart.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $cart = $this->getCart($request);
            $cart->load('items.product.category');

            return response()->json([
                'success' => true,
                'message' => 'Cart retrieved successfully.',
                'data' => new CartResource($cart),
                'session_id' => $cart->session_id // Return session id for guest users
            ]);
        } catch (\Exception $e) {
            Log::error('Error retrieving cart', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving the cart.',
            ], 500);
        }
    }

    /**
     * Add an item to the cart.
     */
    public function add(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|uuid|exists:products,id',
            'quantity' => 'nullable|integer|min:1'
        ]);

        try {
            DB::beginTransaction();

            $cart = $this->getCart($request);
            $productId = $request->input('product_id');
            $quantity = $request->input('quantity', 1);

            $product = Product::findOrFail($productId);

            // Check if product already exists in the cart
            $cartItem = CartItem::where('cart_id', $cart->id)
                ->where('product_id', $productId)
                ->first();

            if ($cartItem) {
                // Update quantity if it already exists
                $cartItem->quantity += $quantity;
                $cartItem->save();
            } else {
                // Create new cart item
                CartItem::create([
                    'id' => Str::uuid(),
                    'cart_id' => $cart->id,
                    'product_id' => $productId,
                    'quantity' => $quantity,
                ]);
            }

            DB::commit();

            $cart->load('items.product.category');

            return response()->json([
                'success' => true,
                'message' => 'Product added to cart successfully.',
                'data' => new CartResource($cart),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error adding to cart', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while adding to the cart.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the quantity of an item in the cart.
     */
    public function update(Request $request, string $cartItemId): JsonResponse
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        try {
            $cartItem = CartItem::findOrFail($cartItemId);
            $cartItem->quantity = $request->input('quantity');
            $cartItem->save();

            $cart = Cart::with('items.product.category')->find($cartItem->cart_id);

            return response()->json([
                'success' => true,
                'message' => 'Cart updated successfully.',
                'data' => new CartResource($cart),
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating cart', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the cart.',
            ], 500);
        }
    }

    /**
     * Remove an item from the cart.
     */
    public function remove(string $cartItemId): JsonResponse
    {
        try {
            $cartItem = CartItem::findOrFail($cartItemId);
            $cartId = $cartItem->cart_id;
            $cartItem->delete();

            $cart = Cart::with('items.product.category')->find($cartId);

            return response()->json([
                'success' => true,
                'message' => 'Product removed from cart successfully.',
                'data' => new CartResource($cart),
            ]);
        } catch (\Exception $e) {
            Log::error('Error removing from cart', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while removing from the cart.',
            ], 500);
        }
    }
    
    /**
     * Clear the entire cart.
     */
    public function clear(Request $request): JsonResponse
    {
        try {
            $cart = $this->getCart($request);
            CartItem::where('cart_id', $cart->id)->delete();
            
            $cart->load('items');

            return response()->json([
                'success' => true,
                'message' => 'Cart cleared successfully.',
                'data' => new CartResource($cart),
            ]);
        } catch (\Exception $e) {
            Log::error('Error clearing cart', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while clearing the cart.',
            ], 500);
        }
    }
}
