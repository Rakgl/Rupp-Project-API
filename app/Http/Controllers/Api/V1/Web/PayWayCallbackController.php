<?php

namespace App\Http\Controllers\Api\V1\Web;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\PayWayService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PayWayCallbackController extends Controller
{
    /**
     * Handle PayWay payment callback (called by ABA after user pays).
     */
    public function handle(Request $request): JsonResponse
    {
        Log::info('PayWay callback received', $request->all());

        $tranId = $request->input('tran_id');

        if (!$tranId) {
            return response()->json(['message' => 'Missing tran_id'], 400);
        }

        $order = Order::where('order_number', $tranId)->first();

        if (!$order) {
            Log::warning('PayWay callback: order not found', ['tran_id' => $tranId]);
            return response()->json(['message' => 'Order not found'], 404);
        }

        if ($order->payment_status === 'PAID') {
            return response()->json(['message' => 'Already paid']);
        }

        // Verify with PayWay to confirm the payment is real
        $payway = app(PayWayService::class);
        $response = $payway->checkTransaction($tranId);

        if (isset($response['status']['code']) && $response['status']['code'] === '00') {
            DB::beginTransaction();
            try {
                $order->update([
                    'payment_status' => 'PAID',
                    'status' => 'PROCESSING',
                ]);

                DB::commit();

                Log::info('PayWay callback: order paid', [
                    'order_number' => $order->order_number,
                    'total' => $order->total_amount,
                ]);

                return response()->json(['message' => 'Payment confirmed']);
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('PayWay callback: failed to update order', [
                    'order_number' => $order->order_number,
                    'error' => $e->getMessage(),
                ]);
                return response()->json(['message' => 'Internal error'], 500);
            }
        }

        Log::warning('PayWay callback: payment not confirmed by check-transaction', [
            'order_number' => $order->order_number,
            'payway_response' => $response,
        ]);

        return response()->json(['message' => 'Payment not confirmed'], 422);
    }
}
