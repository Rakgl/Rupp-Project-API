<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Helpers\AppHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\Admin\Product\ProductIndexResource;
use App\Http\Resources\Api\V1\Admin\Product\ProductShowResource;
use App\Models\Product;
use App\Models\Store;
use App\Models\StoreInventory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Product::with(['category', 'storeInventories']);

            // Search by name
            if ($request->has('search') && !empty($request->input('search'))) {
                $searchTerm = strtolower($request->input('search'));
                // Cast the JSON column to text before applying LOWER()
                $query->whereRaw('LOWER(name::text) LIKE ?', ['%' . $searchTerm . '%']);
            }

            // Filter by status
            if ($request->has('status')) {
                $query->where('status', $request->input('status'));
            }

            $products = $query->latest()->paginate($request->input('per_page', 10));
            $resource   = ProductIndexResource::collection($products)->response()->getData(true);

            return response()->json([
                'success' => true,
                'message' => 'Products retrieved successfully.',
                'data'    => $resource['data'],
                'meta'    => $resource['meta'] ?? null,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving products.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'category_id' => 'required|uuid|exists:categories,id',
            'name' => 'required', // Can be string or array
            'slug' => 'nullable|string|unique:products,slug',
            'description' => 'nullable', // Can be string or array
            'attributes' => 'nullable', // Can be string (JSON) or array
            'price' => 'required|numeric|min:0',
            'sku' => 'nullable|string|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'image_url' => 'nullable|string',
            'status' => 'nullable|string|in:ACTIVE,INACTIVE',
            'stock_quantity' => 'nullable|integer|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        try {
            DB::beginTransaction();

            $validated['status'] = $validated['status'] ?? 'ACTIVE';
            $stockQuantity = $validated['stock_quantity'] ?? 0;
            unset($validated['stock_quantity']);

            // Handle image upload
            if ($request->hasFile('image')) {
                $validated['image_url'] = AppHelper::uploadImage($request->file('image'), 'uploads/products');
            }
            unset($validated['image']);

            // Handle name structure (string to array)
            if (is_string($validated['name'])) {
                $nameText = $validated['name'];
                $validated['name'] = ['en' => $nameText, 'kh' => $nameText, 'zh' => $nameText];
            }

            // Handle description structure
            if (isset($validated['description']) && is_string($validated['description'])) {
                $descText = $validated['description'];
                $validated['description'] = ['en' => $descText, 'kh' => $descText, 'zh' => $descText];
            }

            // Handle attributes structure (JSON string to array)
            if (isset($validated['attributes']) && is_string($validated['attributes'])) {
                $decoded = json_decode($validated['attributes'], true);
                $validated['attributes'] = is_array($decoded) ? $decoded : [];
            }

            // Auto-generate slug if missing
            if (empty($validated['slug'])) {
                $nameForSlug = is_array($validated['name']) ? ($validated['name']['en'] ?? reset($validated['name'])) : $validated['name'];
                $validated['slug'] = Str::slug($nameForSlug) . '-' . Str::random(5);
            }

            $product = Product::create($validated);

            // Default to first store for inventory management
            $store = Store::first();
            if ($store) {
                StoreInventory::create([
                    'id' => Str::uuid(),
                    'store_id' => $store->id,
                    'product_id' => $product->id,
                    'stock_quantity' => $stockQuantity
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Product created successfully.',
                'data' => new ProductShowResource($product->load('category'))
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating product.',
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
            $product = Product::with(['category', 'storeInventories'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Product details retrieved successfully.',
                'data'    => new ProductShowResource($product),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving product.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $product = Product::findOrFail($id);

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'category_id' => 'nullable|uuid|exists:categories,id',
            'name' => 'nullable',
            'slug' => 'nullable|string|unique:products,slug,' . $id,
            'description' => 'nullable',
            'attributes' => 'nullable', // Can be string or array
            'price' => 'nullable|numeric|min:0',
            'sku' => 'nullable|string|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'image_url' => 'nullable|string',
            'delete_image' => 'nullable|boolean',
            'status' => 'nullable|string|in:ACTIVE,INACTIVE',
            'stock_quantity' => 'nullable|integer|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        try {
            DB::beginTransaction();

            $stockQuantity = $validated['stock_quantity'] ?? null;
            unset($validated['stock_quantity']);

            // Handle image update or removal
            if ($request->hasFile('image')) {
                if ($product->image_url && Storage::exists($product->image_url)) {
                    Storage::delete($product->image_url);
                }
                $validated['image_url'] = AppHelper::uploadImage($request->file('image'), 'uploads/products');
            } elseif ($request->input('delete_image')) {
                if ($product->image_url && Storage::exists($product->image_url)) {
                    Storage::delete($product->image_url);
                }
                $validated['image_url'] = null;
            }
            unset($validated['image']);
            unset($validated['delete_image']);

            // Handle name structure
            if (isset($validated['name']) && is_string($validated['name'])) {
                $nameText = $validated['name'];
                $validated['name'] = ['en' => $nameText, 'kh' => $nameText, 'zh' => $nameText];
            }

            // Handle description structure
            if (isset($validated['description']) && is_string($validated['description'])) {
                $descText = $validated['description'];
                $validated['description'] = ['en' => $descText, 'kh' => $descText, 'zh' => $descText];
            }

            // Handle attributes structure
            if (isset($validated['attributes']) && is_string($validated['attributes'])) {
                $decoded = json_decode($validated['attributes'], true);
                $validated['attributes'] = is_array($decoded) ? $decoded : [];
            }

            $product->update($validated);

            if ($stockQuantity !== null) {
                $store = Store::first();
                if ($store) {
                    StoreInventory::updateOrCreate(
                        ['store_id' => $store->id, 'product_id' => $product->id],
                        ['stock_quantity' => $stockQuantity]
                    );
                }
            }

            DB::commit();

            $product->load(['category', 'storeInventories']);

            return response()->json([
                'success' => true,
                'message' => 'Product updated successfully.',
                'data' => new ProductShowResource($product)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating product.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $product = Product::findOrFail($id);
        
        if ($product->image_url && Storage::exists($product->image_url)) {
            Storage::delete($product->image_url);
        }

        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully.'
        ]);
    }
}
