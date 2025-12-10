<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Model as VehicleModel;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ModelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $brandId = $request->input('brand_id');

        $models = VehicleModel::with('brand:id,name,image_url')
            ->withCount(['reviews'])
            ->when($search, function ($query, $search) {
                $searchTerm = strtolower($search);
                $query->whereRaw('LOWER(name) like ?', ["%{$searchTerm}%"])
                    ->orWhereHas('brand', function ($q) use ($searchTerm) {
                        $q->whereRaw('LOWER(name) like ?', ["%{$searchTerm}%"]);
                    });
            })
            ->when($brandId, function ($query, $brandId) {
                $query->where('brand_id', $brandId);
            })
            ->latest()
            ->paginate(10);

        return response()->json($models);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'brand_id' => 'required|exists:brands,id',
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('models')->where(function ($query) use ($request) {
                        return $query->where('brand_id', $request->brand_id);
                    })
                ]
            ], [
                'name.unique' => 'This model already exists for the selected brand.'
            ]);

            $model = VehicleModel::create($data);

            return response()->json($model, 201);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to create model.', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(VehicleModel $model)
    {
        $model->load(['brand:id,name', 'reviews']);

        return response()->json($model);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, VehicleModel $model)
    {
        try {
            $data = $request->validate([
                'brand_id' => 'required|exists:brands,id',
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('models')->where(function ($query) use ($request) {
                        return $query->where('brand_id', $request->brand_id);
                    })->ignore($model->id)
                ]
            ], [
                'name.unique' => 'This model already exists for the selected brand.'
            ]);

            $model->update($data);

            return response()->json($model);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to update model.', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(VehicleModel $model)
    {
        try {
            // Check if model has associated cars, listings, or reviews
            $hasRelations = $model->cars()->count() > 0
                || $model->userListings()->count() > 0
                || $model->reviews()->count() > 0;

            if ($hasRelations) {
                return response()->json(['error' => 'Cannot delete model with associated cars, listings, or reviews.'], 400);
            }

            $model->delete();

            return response()->noContent();
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete model.', 'message' => $e->getMessage()], 500);
        }
    }
}