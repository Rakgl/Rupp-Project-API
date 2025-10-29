<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Helpers\AppHelper;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\Admin\Store\StoreIndexResource;
use App\Http\Resources\Api\V1\Admin\Store\StoreShowResource;

class StoreController extends Controller
{
	public function index(Request $request)
    {
        try {
            $query = Store::query();

            // Handle the search parameter
            if ($request->has('search') && !empty($request->input('search'))) {
                $searchTerm = $request->input('search');
                $query->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($searchTerm) . '%']);
            }

            // Handle the status filter
            if ($request->has('status')) {
                $query->where('status', $request->input('status'));
            }
            
            // Handle the verification filter
            if ($request->has('is_verified')) {
                // Securely filter boolean values
                $query->where('is_verified', filter_var($request->input('is_verified'), FILTER_VALIDATE_BOOLEAN));
            }

            $stores = $query->latest()->paginate($request->input('per_page', 15));
            $resource = StoreIndexResource::collection($stores)->response()->getData(true);

            return response()->json([
                'success' => true,
                'message' => 'Stores retrieved successfully.',
                'data' => $resource['data'],
                'meta' => $resource['meta'] ?? null // Ensure meta is always present
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving stores.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'city' => 'required|string|max:255',
            'state' => 'nullable|string|max:255',
            'zip_code' => 'required|string|max:20',
            'country' => 'required|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'phone_number' => 'nullable|string|max:25',
            'email' => 'nullable|email|max:255|unique:stores,email',
            'license_number' => 'nullable|string|max:255|unique:stores,license_number',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048', // Validation for logo file
            'opening_time' => 'nullable|date_format:H:i',
            'closing_time' => 'nullable|date_format:H:i|after:opening_time',
            'is_24_hours' => 'required|boolean',
            'delivers_product' => 'required|boolean',
            'delivery_details' => 'nullable|string|required_if:delivers_product,true',
            'is_highlighted' => 'sometimes|boolean',
            'is_top_choice' => 'sometimes|boolean',
            'is_verified' => 'required|boolean',
            'status' => 'required|in:ACTIVE,INACTIVE',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = $validator->validated();
            
            if ($request->hasFile('logo')) {
				$imagePath = AppHelper::uploadImage($request->file('logo'), 'uploads/images');
				$data['logo_url'] = $imagePath;
            }
            unset($data['logo']);

            $data['created_by'] = auth()->id();
            $data['updated_by'] = auth()->id();

            $store = Store::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Store created successfully.',
                'data' => $store
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error creating store: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the store.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Store  $store
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Store $store)
    {
        return response()->json([
            'success' => true,
            'message' => 'Store details retrieved successfully.',
            'data' => new StoreShowResource($store)
        ]);
    }

    /**
     * Update the specified resource in storage.
     * Note: For file uploads with PUT/PATCH, use a POST request with a _method="PUT" field.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Store  $store
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Store $store)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'address' => 'sometimes|string',
            'city' => 'sometimes|string|max:255',
            'state' => 'nullable|string|max:255',
            'zip_code' => 'sometimes|string|max:20',
            'country' => 'sometimes|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'phone_number' => 'nullable|string|max:25',
            'email' => 'nullable|email|max:255|unique:stores,email,' . $store->id,
            'license_number' => 'nullable|string|max:255|unique:stores,license_number,' . $store->id,
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'opening_time' => 'nullable|date_format:H:i',
            'closing_time' => 'nullable|date_format:H:i|after:opening_time',
            'is_24_hours' => 'sometimes|required|boolean',
            'delivers_product' => 'sometimes|required|boolean',
            'delivery_details' => 'nullable|string|required_if:delivers_product,true',
            'is_verified' => 'sometimes|required|boolean',
            'status' => 'sometimes|required|in:ACTIVE,INACTIVE',
            // Add validation for the delete_logo flag
            'delete_logo' => 'sometimes|boolean',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation errors', 'errors' => $validator->errors()], 422);
        }

        try {
            $data = $validator->validated();

            // Handle logo update or removal
            if ($request->hasFile('logo')) {
                // 1. A new logo is uploaded, so delete the old one and upload the new one.
                if ($store->logo_url && Storage::exists($store->logo_url)) {
                    Storage::delete($store->logo_url);
                }
                $logoPath = AppHelper::uploadImage($request->file('logo'), 'store_logos');
                $data['logo_url'] = $logoPath;
            } elseif ($request->input('delete_logo') == '1') {
                if ($store->logo_url && Storage::exists($store->logo_url)) {
                    Storage::delete($store->logo_url);
                }
                $data['logo_url'] = null;
            }
            
            // Unset temporary fields so they are not mass-assigned
            unset($data['logo']);
            unset($data['delete_logo']);

            $store->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Store updated successfully.',
                'data' => new StoreShowResource($store->fresh())
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating store: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'An error occurred while updating the store.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Store  $store
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Store $store)
    {
        try {
            if ($store->logo_url) {
                $path = Str::replace(Storage::url('/'), '', $store->logo_url);
                if (Storage::exists($path)) {
                    Storage::delete($path);
                }
            }

            $store->delete();

            return response()->json([
                'success' => true,
                'message' => 'Store deleted successfully.'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error deleting store: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting the store.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}