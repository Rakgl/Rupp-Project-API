<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\Admin\StoreInventory\StoreInventoryIndexResource;
use App\Http\Resources\Api\V1\Admin\StoreInventory\StoreInventoryShowResource;
use App\Models\StoreInventory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StoreInventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->query('per_page', 15);
            $query = StoreInventory::with(['store', 'product']);

            if ($request->has('store_id')) {
                $query->where('store_id', $request->query('store_id'));
            }
            
            if ($request->has('product_id')) {
                $query->where('product_id', $request->query('product_id'));
            }

            $inventories = $query->paginate($perPage);
            $resource = StoreInventoryIndexResource::collection($inventories)->response()->getData(true);

            return response()->json([
                'success' => true,
                'message' => 'Store Inventories retrieved successfully.',
                'data' => $resource['data'],
                'meta' => $resource['meta'] ?? null
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving inventories.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store or Update a resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'store_id' => 'required|uuid|exists:stores,id',
            'product_id' => 'required|uuid|exists:products,id',
            'stock_quantity' => 'required|integer|min:0'
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
            $inventory = StoreInventory::updateOrCreate(
                [
                    'store_id' => $validated['store_id'],
                    'product_id' => $validated['product_id']
                ],
                [
                    'stock_quantity' => $validated['stock_quantity']
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Inventory updated successfully.',
                'data' => new StoreInventoryShowResource($inventory)
            ], 201);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error creating store inventory: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating/updating the inventory.',
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
            $inventory = StoreInventory::with(['store', 'product'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Store inventory retrieved successfully.',
                'data' => new StoreInventoryShowResource($inventory)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving inventory details.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'stock_quantity' => 'required|integer|min:0'
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
            $inventory = StoreInventory::findOrFail($id);
            $inventory->update($validated);

            $inventory->load(['store', 'product']);

            return response()->json([
                'success' => true,
                'message' => 'Inventory updated successfully.',
                'data' => new StoreInventoryShowResource($inventory)
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error updating store inventory: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the inventory.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $inventory = StoreInventory::findOrFail($id);
            $inventory->delete();

            return response()->json([
                'success' => true,
                'message' => 'Inventory record deleted successfully.'
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error deleting store inventory: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting the inventory.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
