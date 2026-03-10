<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\Admin\Cart\CartIndexResource;
use App\Http\Resources\Api\V1\Admin\Cart\CartShowResource;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Cart::with('user')->withCount('items');

            if ($request->has('user_id')) {
                $query->where('user_id', $request->input('user_id'));
            }

            if ($request->has('session_id')) {
                $query->where('session_id', $request->input('session_id'));
            }

            if ($request->has('status')) {
                $query->where('status', $request->input('status'));
            }

            $carts = $query->latest()->paginate($request->input('per_page', 10));
            $resource = CartIndexResource::collection($carts)->response()->getData(true);

            return response()->json([
                'success' => true,
                'message' => 'Carts retrieved successfully.',
                'data'    => $resource['data'],
                'meta'    => $resource['meta'] ?? null,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving carts.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Convert a Cart into a sellable Product.
     * This takes the contents of a cart and creates a new Product entry.
     */
    public function convertToProduct(Request $request, string $id): JsonResponse
    {
        try {
            $cart = Cart::with(['items.product', 'user'])->findOrFail($id);

            if ($cart->items->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot convert an empty cart to a product.'
                ], 422);
            }

            return DB::transaction(function () use ($cart, $request) {
                $totalPrice = $cart->items->sum(function ($item) {
                    return $item->product->price * $item->quantity;
                });
                $userName = $cart->user ? $cart->user->name : 'Guest';
                $productName = "Bundle from {$userName}'s Cart " . now()->format('Y-m-d');

                $product = Product::create([
                    'category_id' => $request->input('category_id', $cart->items->first()->product->category_id),
                    'name' => [
                        'en' => $request->input('name', $productName),
                    ],
                    'slug' => Str::slug($request->input('name', $productName)) . '-' . Str::random(5),
                    'description' => [
                        'en' => "This is a curated bundle created from a customer cart. Includes " . $cart->items->count() . " items.",
                    ],
                    'price' => $request->input('price', $totalPrice),
                    'status' => 'INACTIVE', // Default to inactive so admin can review
                    'attributes' => [
                        'converted_from_cart' => $cart->id,
                        'original_item_count' => $cart->items->count(),
                        'items_summary' => $cart->items->map(fn($i) => $i->product->name['en'] ?? 'Product')->toArray()
                    ]
                ]);

                $cart->update(['status' => 'CONVERTED']);

                return response()->json([
                    'success' => true,
                    'message' => 'Cart successfully converted to a new product bundle.',
                    'data' => $product
                ], 201);
            });

        } catch (\Exception $e) {
            Log::error('Cart conversion failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to convert cart to product.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $cart = Cart::with(['user', 'items.product.category'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Cart details retrieved successfully.',
                'data'    => new CartShowResource($cart),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving cart.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $cart = Cart::findOrFail($id);
            $cart->delete();

            return response()->json([
                'success' => true,
                'message' => 'Cart deleted successfully.'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting cart: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting the cart.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}