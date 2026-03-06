<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\Admin\Product\ProductIndexResource;
use App\Http\Resources\Api\V1\Admin\Product\ProductShowResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Product::with('category');

            // Search by name
            if ($request->has('search') && !empty($request->input('search'))) {
                $searchTerm = $request->input('search');
                $query->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($searchTerm) . '%']);
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
        $validated = $request->validate([
            'category_id' => 'required|uuid|exists:categories,id',
            'name' => 'required|array',
            'name.en' => 'required|string',
            'slug' => 'required|string|unique:products,slug',
            'description' => 'nullable|array',
            'attributes' => 'nullable|array',
            'attributes.gender' => 'nullable|string',
            'attributes.age' => 'nullable|string',
            'attributes.color' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'image_url' => 'nullable|string',
            'status' => 'nullable|string|in:ACTIVE,INACTIVE'
        ]);

        $validated['status'] = $validated['status'] ?? 'ACTIVE';

        $product = Product::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Product created successfully.',
            'data' => $product
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $product = Product::with('category')->findOrFail($id);

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

        $validated = $request->validate([
            'category_id' => 'nullable|uuid|exists:categories,id',
            'name' => 'nullable|array',
            'name.en' => 'nullable|string',
            'slug' => 'nullable|string|unique:products,slug,' . $id,
            'description' => 'nullable|array',
            'attributes' => 'nullable|array',
            'attributes.gender' => 'nullable|string',
            'attributes.age' => 'nullable|string',
            'attributes.color' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'image_url' => 'nullable|string',
            'status' => 'nullable|string|in:ACTIVE,INACTIVE'
        ]);

        $product->update($validated);

        $product->load('category');

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully.',
            'data' => new ProductShowResource($product)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully.'
        ]);
    }
}
