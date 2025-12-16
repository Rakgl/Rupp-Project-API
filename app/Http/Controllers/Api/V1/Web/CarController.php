<?php
namespace App\Http\Controllers\Api\V1\Web;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\Admin\Car\CarResource;
use App\Models\Car;
use Illuminate\Http\Request;
    
class CarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $cars = Car::with([
                'model:id,name,brand_id',
                'model.brand:id,name,image_url',
                'bodyType:id,name',
                'images',
            ])
            ->withCount('images')
            ->when($request->input('brand_id'), function ($query, $brand_id) {
                $query->whereHas('model', function ($q) use ($brand_id) {
                    $q->where('brand_id', $brand_id);
                });
            })
            ->when($request->input('model_id'), function ($query, $model_id) {
                $query->where('model_id', $model_id);
            })
            ->when($request->input('body_type_id'), function ($query, $body_type_id) {
                $query->where('body_type_id', $body_type_id);
            })
            ->when($request->input('year'), function ($query, $year) {
                $query->where('year', $year);
            })
            ->when($request->input('price_min'), function ($query, $price_min) {
                $query->where('price', '>=', $price_min);
            })
            ->when($request->input('price_max'), function ($query, $price_max) {
                $query->where('price', '<=', $price_max);
            })
            ->when($request->input('fuel_type'), function ($query, $fuel_type) {
                $query->where('fuel_type', $fuel_type);
            })
            ->when($request->input('condition'), function ($query, $condition) {
                $query->where('condition', $condition);
            })
            ->when($request->input('transmission'), function ($query, $transmission) {
                $query->where('transmission', $transmission);
            })
            ->when($request->input('lease_price_per_month_min'), function ($query, $lease_price_per_month_min) {
                $query->where('lease_price_per_month', '>=', $lease_price_per_month_min);
            })
            ->when($request->input('lease_price_per_month_max'), function ($query, $lease_price_per_month_max) {
                $query->where('lease_price_per_month', '<=', $lease_price_per_month_max);
            })
            ->latest()
            ->paginate($request->input('per_page', 10));

        return CarResource::collection($cars);
    }

    /**
     * Display the specified resource.
     */
    public function show(Car $car)
    {
        $car->load([
            'model:id,name,brand_id',
            'model.brand:id,name,image_url',
            'bodyType:id,name',
            'images',
        ]);

        return new CarResource($car);
    }
}
