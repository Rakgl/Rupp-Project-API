<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\PetListing;
use Illuminate\Http\Request;
use App\Http\Resources\Api\V1\Admin\PetListing\PetListingIndexResource;
use App\Http\Resources\Api\V1\Admin\PetListing\PetListingShowResource;
use Illuminate\Http\JsonResponse;

class PetListingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = PetListing::query()->with(['pet', 'user']);

            if ($request->has('search')) {
                $searchTerm = $request->input('search');
                $query->whereHas('pet', function ($q) use ($searchTerm) {
                    $q->where('name', 'like', "%{$searchTerm}%")
                      ->orWhere('species', 'like', "%{$searchTerm}%")
                      ->orWhere('breed', 'like', "%{$searchTerm}%");
                });
            }

            if ($request->has('status')) {
                $query->where('status', $request->input('status'));
            }

            $listings = $query->latest()->paginate($request->input('per_page', 10));
            $resource = PetListingIndexResource::collection($listings)->response()->getData(true);

            return response()->json([
                'success' => true,
                'message' => 'Pet listings retrieved successfully.',
                'data'    => $resource['data'],
                'meta'    => $resource['meta'] ?? null,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving pet listings.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'pet_id' => 'required|exists:pets,id',
                'listing_type' => 'required|string|in:SALE,ADOPTION',
                'price' => 'nullable|numeric|required_if:listing_type,SALE',
                'description' => 'nullable|string',
                'status' => 'nullable|string|in:AVAILABLE,PENDING,SOLD',
            ]);

            $validated['user_id'] = $request->user()->id;
            
            $listing = PetListing::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Pet listing created successfully.',
                'data' => new PetListingShowResource($listing->load(['pet', 'user']))
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the pet listing.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(PetListing $petListing): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'message' => 'Pet listing details retrieved successfully.',
                'data'    => new PetListingShowResource($petListing->load(['pet', 'user'])),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving pet listing details.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PetListing $petListing): JsonResponse
    {
        try {
            $validated = $request->validate([
                'listing_type' => 'sometimes|required|string|in:SALE,ADOPTION',
                'price' => 'nullable|numeric',
                'description' => 'nullable|string',
                'status' => 'sometimes|required|string|in:AVAILABLE,PENDING,SOLD',
            ]);

            $petListing->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Pet listing updated successfully.',
                'data' => new PetListingShowResource($petListing->load(['pet', 'user']))
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the pet listing.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PetListing $petListing): JsonResponse
    {
        try {
            $petListing->delete();

            return response()->json([
                'success' => true,
                'message' => 'Pet listing deleted successfully.'
            ], 204);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting the pet listing.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
