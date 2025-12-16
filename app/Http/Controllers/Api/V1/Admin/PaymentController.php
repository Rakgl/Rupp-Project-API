<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\Payment\StorePaymentRequest;
use App\Http\Requests\Api\V1\Admin\Payment\UpdatePaymentRequest;
use App\Http\Resources\Api\V1\Admin\Payment\PaymentResource;
use App\Models\Payment;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    /**
     * Display a listing of payments.
     */
    public function index(Request $request)
    {
        $status = $request->input('status');
        $userId = $request->input('user_id');
        $payableType = $request->input('payable_type');
        $payableId = $request->input('payable_id');

        $payments = Payment::with(['user:id,name,email', 'payable'])
            ->when($status, fn($q) => $q->where('status', $status))
            ->when($userId, fn($q) => $q->where('user_id', $userId))
            ->when($payableType, fn($q) => $q->where('payable_type', $payableType))
            ->when($payableId, fn($q) => $q->where('payable_id', $payableId))
            ->latest()
            ->paginate($request->input('per_page', 10));

        return PaymentResource::collection($payments);
    }

    /**
     * Store a newly created payment.
     */
    public function store(StorePaymentRequest $request)
    {
        $data = $request->validated();

        try {
            $payment = Payment::create($data);
            $payment->load(['user:id,name,email', 'payable']);
            return new PaymentResource($payment);
        } catch (Exception $e) {
            Log::error('Failed to create payment', [
                'data' => $data,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error' => 'Failed to create payment.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified payment.
     */
    public function show(Payment $payment)
    {
        $payment->load(['user:id,name,email', 'payable']);
        return new PaymentResource($payment);
    }

    /**
     * Update the specified payment.
     */
    public function update(UpdatePaymentRequest $request, Payment $payment)
    {
        $data = $request->validated();

        try {
            $payment->update($data);
            $payment->load(['user:id,name,email', 'payable']);
            return new PaymentResource($payment);
        } catch (Exception $e) {
            Log::error('Failed to update payment', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error' => 'Failed to update payment.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified payment.
     */
    public function destroy(Payment $payment)
    {
        try {
            $payment->delete();
            return response()->noContent();
        } catch (Exception $e) {
            Log::error('Failed to delete payment', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error' => 'Failed to delete payment.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
