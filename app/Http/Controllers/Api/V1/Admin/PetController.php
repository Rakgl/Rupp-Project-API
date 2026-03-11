<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pet;
use Illuminate\Http\Request;
use App\Http\Resources\Api\V1\Admin\Pet\PetIndexResource;
use App\Http\Resources\Api\V1\Admin\Pet\PetShowResource;
use Illuminate\Http\JsonResponse;

class PetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Pet::query();

            if ($request->has('search')) {
                $searchTerm = $request->input('search');
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('name', 'like', "%{$searchTerm}%")
                      ->orWhere('species', 'like', "%{$searchTerm}%")
                      ->orWhere('breed', 'like', "%{$searchTerm}%");
                });
            }

            if ($request->has('user_id')) {
                $query->where('user_id', $request->input('user_id'));
            }

            if ($request->has('category_id')) {
                $query->where('category_id', $request->input('category_id'));
            }

            $pets = $query->latest()->paginate($request->input('per_page', 10));
            $resource = PetIndexResource::collection($pets)->response()->getData(true);

            return response()->json([
                'success' => true,
                'message' => 'Pets retrieved successfully.',
                'data'    => $resource['data'],
                'meta'    => $resource['meta'] ?? null,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving pets.',
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
                'user_id' => 'required|exists:users,id',
                'category_id' => 'nullable|exists:categories,id',
                'name' => 'required|string|max:255',
                'species' => 'required|string|max:255',
                'breed' => 'required|string|max:255',
                'weight' => 'nullable|numeric',
                'date_of_birth' => 'nullable|date',
                'image_url' => 'nullable|url',
                'medical_notes' => 'nullable|string',
            ]);

            $pet = Pet::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Pet created successfully.',
                'data' => new PetShowResource($pet)
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the pet.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Pet $pet): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'message' => 'Pet details retrieved successfully.',
                'data'    => new PetShowResource($pet),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving pet details.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pet $pet): JsonResponse
    {
        try {
            $validated = $request->validate([
                'category_id' => 'sometimes|nullable|exists:categories,id',
                'name' => 'sometimes|required|string|max:255',
                'species' => 'sometimes|required|string|max:255',
                'breed' => 'sometimes|required|string|max:255',
                'weight' => 'nullable|numeric',
                'date_of_birth' => 'nullable|date',
                'image_url' => 'nullable|url',
                'medical_notes' => 'nullable|string',
            ]);

            $pet->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Pet updated successfully.',
                'data' => new PetShowResource($pet)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the pet.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pet $pet): JsonResponse
    {
        try {
            $pet->delete();

            return response()->json([
                'success' => true,
                'message' => 'Pet deleted successfully.'
            ], 204);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting the pet.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
