<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Http\Resources\Api\V1\Admin\Order\OrderIndexResource;
use App\Http\Resources\Api\V1\Admin\Order\OrderShowResource;
use App\Http\Resources\Api\V1\Admin\Order\OrderItemResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    /**
     * Display a listing of the orders.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->query('per_page', 15);
            $query = Order::with(['user', 'store', 'paymentMethod']);

            // Handle status filtering
            if ($request->has('status')) {
                $query->where('status', $request->query('status'));
            }

            // Handle payment status filtering
            if ($request->has('payment_status')) {
                $query->where('payment_status', $request->query('payment_status'));
            }

            // Handle fulfillment type filtering
            if ($request->has('fulfillment_type')) {
                $query->where('fulfillment_type', $request->query('fulfillment_type'));
            }

            // Handle sorting
            $query->latest();

            $orders = $query->paginate($perPage);
            $resource = OrderIndexResource::collection($orders)->response()->getData(true);

            return response()->json([
                'success' => true,
                'message' => 'Orders retrieved successfully.',
                'data' => $resource['data'],
                'meta' => $resource['meta'] ?? null
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving orders.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified order.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $order = Order::with(['user', 'store', 'paymentMethod', 'orderItems.product'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Order retrieved successfully.',
                'data' => new OrderShowResource($order)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving order details.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified order.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'payment_method_id' => 'nullable|uuid|exists:payment_methods,id',
            'fulfillment_type' => 'sometimes|in:PICKUP,DELIVERY',
            'status' => 'sometimes|in:PENDING,PROCESSING,READY,COMPLETED,CANCELLED',
            'payment_status' => 'sometimes|in:UNPAID,PAID,FAILED',
            'delivery_address' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $order = Order::findOrFail($id);
            $order->update($validator->validated());
            $order->load(['user', 'store', 'paymentMethod', 'orderItems.product']);

            return response()->json([
                'success' => true,
                'message' => 'Order updated successfully.',
                'data' => new OrderShowResource($order)
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating order: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the order.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the status of the specified order.
     */
    public function updateStatus(Request $request, string $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:PENDING,PROCESSING,READY,COMPLETED,CANCELLED'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $order = Order::findOrFail($id);
            $order->update(['status' => $request->status]);

            return response()->json([
                'success' => true,
                'message' => 'Order status updated successfully.',
                'data' => ['status' => $order->status]
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating order status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the order status.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Fetch the line items for the specified order.
     */
    public function getItems(string $id): JsonResponse
    {
        try {
            $order = Order::with('orderItems.product')->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'message' => 'Order items retrieved successfully.',
                'data' => OrderItemResource::collection($order->orderItems)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving order items.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
