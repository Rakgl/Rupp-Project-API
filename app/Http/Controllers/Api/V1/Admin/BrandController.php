<?php
namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\AppHelper;
use App\Http\Resources\Api\V1\Admin\Brand\BrandResource;
use App\Models\Brand;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        $brands = Brand::withCount('models')
            ->when($search, function ($query, $search) {
                $searchTerm = strtolower($search);
                $query->whereRaw('LOWER(name) like ?', ["%{$searchTerm}%"]);
            })
            ->latest()
            ->paginate(10);
            
        return BrandResource::collection($brands);
    }

    public function all()
    {
        $brands = Brand::orderBy('name')->get(['id', 'name']);

        return BrandResource::collection($brands);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'name' => 'required|string|max:255|unique:brands,name',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            if ($request->hasFile('image')) {
                $imagePath = AppHelper::uploadImage($request->file('image'), 'uploads/brands');
                
                $data['image_url'] = $imagePath;
                
                unset($data['image']);
            }

            $brand = Brand::create($data);

            return new BrandResource($brand);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to create brand.', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Brand $brand)
    {
        $brand->load('models');
        
        return new BrandResource($brand);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Brand $brand)
    {
        try {
            $data = $request->validate([
                'name' => 'required|string|max:255|unique:brands,name,' . $brand->id,
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            // Handle Image Update
            if ($request->hasFile('image')) {
                // Delete the old image
                if ($brand->image_url) {
                    Storage::delete($brand->image_url);
                }
                
                $imagePath = AppHelper::uploadImage($request->file('image'), 'uploads/brands');
                $data['image_url'] = $imagePath;
                unset($data['image']);
            }

            $brand->update($data);
            
            return new BrandResource($brand);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to update brand.', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Brand $brand)
    {
        try {
            // Check if brand has associated models
            if ($brand->models()->count() > 0) {
                return response()->json(['error' => 'Cannot delete brand with associated models.'], 400);
            }

            // Delete image if exists
            if ($brand->image_url) {
                Storage::delete($brand->image_url);
            }

            $brand->delete();
            
            return response()->noContent();
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete brand.', 'message' => $e->getMessage()], 500);
        }
    }
}