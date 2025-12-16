<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\CarSale\StoreCarSaleRequest;
use App\Http\Requests\Api\V1\Admin\CarSale\UpdateCarSaleRequest;
use App\Http\Resources\Api\V1\Admin\CarSale\CarSaleResource;
use App\Models\CarSale;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CarSaleController extends Controller
{
    /**
     * Display a listing of car sales.
     */
    public function index(Request $request)
    {
        $status = $request->input('status');
        $buyerId = $request->input('buyer_id');
        $carId = $request->input('car_id');

        $sales = CarSale::with([
                'car:id,model_id',
                'car.model:id,name,brand_id',
                'car.model.brand:id,name,image_url',
                'buyer:id,name,email',
            ])
            ->when($status, fn($q) => $q->where('status', $status))
            ->when($buyerId, fn($q) => $q->where('buyer_id', $buyerId))
            ->when($carId, fn($q) => $q->where('car_id', $carId))
            ->latest()
            ->paginate($request->input('per_page', 10));

        return CarSaleResource::collection($sales);
    }

    /**
     * Store a newly created car sale.
     */
    public function store(StoreCarSaleRequest $request)
    {
        $data = $request->validated();

        try {
            $sale = CarSale::create($data);

            $sale->load([
                'car:id,model_id',
                'car.model:id,name,brand_id',
                'car.model.brand:id,name,image_url',
                'buyer:id,name,email',
            ]);

            return new CarSaleResource($sale);
        } catch (Exception $e) {
            Log::error('Failed to create car sale', [
                'data' => $data,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error' => 'Failed to create car sale.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified car sale.
     */
    public function show(CarSale $carSale)
    {
        $carSale->load([
            'car:id,model_id',
            'car.model:id,name,brand_id',
            'car.model.brand:id,name,image_url',
            'buyer:id,name,email',
            'payments',
        ]);

        return new CarSaleResource($carSale);
    }

    /**
     * Update the specified car sale.
     */
    public function update(UpdateCarSaleRequest $request, CarSale $carSale)
    {
        $data = $request->validated();
        $oldStatus = $carSale->status;

        try {
            $carSale->update($data);

            $carSale->load([
                'car:id,model_id',
                'car.model:id,name,brand_id',
                'car.model.brand:id,name,image_url',
                'buyer:id,name,email',
            ]);

            // Notify buyer when sale moves to payment_pending
            if (($data['status'] ?? $oldStatus) === 'payment_pending' && $oldStatus !== 'payment_pending') {
                Log::info('Car sale moved to payment_pending', ['car_sale_id' => $carSale->id, 'buyer_id' => $carSale->buyer_id]);
            }

            return new CarSaleResource($carSale);
        } catch (Exception $e) {
            Log::error('Failed to update car sale', [
                'car_sale_id' => $carSale->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error' => 'Failed to update car sale.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified car sale.
     */
    public function destroy(CarSale $carSale)
    {
        try {
            $carSale->delete();
            return response()->noContent();
        } catch (Exception $e) {
            Log::error('Failed to delete car sale', [
                'car_sale_id' => $carSale->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error' => 'Failed to delete car sale.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
