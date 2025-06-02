<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Speciality; // Assuming your Speciality model is in App\Models
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class SpecialityController extends Controller
{
    /**
     * Display a listing of the specialities.
     *
     * Retrieves a paginated list of active specialities.
     *
     * @queryParam page int The page number to retrieve. Example: 1
     * @queryParam per_page int The number of items per page. Example: 15
     * @queryParam search string A search term to filter specialities by name. Example: "Cardiology"
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        // Validate incoming request parameters
        $validator = Validator::make($request->all(), [
            'per_page' => 'integer|min:1|max:100',
            'search' => 'string|nullable|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // Get the number of items per page, default to 15
            $perPage = $request->input('per_page', 15);

            // Start building the query
            // Status is now a string 'ACTIVE' as per the updated migration
            $query = Speciality::query()->where('status', 'ACTIVE');

            // Apply search filter if provided for the name
            if ($request->filled('search')) {
                $searchTerm = '%' . $request->input('search') . '%';
                $query->where('name', 'like', $searchTerm);
            }

            // Select specific columns to return for efficiency
            // Adjust if 'image' or 'description' are too large for a list view
            $specialities = $query->select(
                'id',
                'name',
                'image',       // URL or path to the image
                'description'
            )
            ->orderBy('name', 'asc') // Default ordering by name
            ->paginate($perPage);

            // Return a successful response
            return response()->json([
                'success' => true,
                'message' => 'Specialities retrieved successfully.',
                'data' => $specialities,
            ], 200);

        } catch (\Exception $e) {
            // \Log::error('Error fetching specialities: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching specialities.',
                // 'error_detail' => $e->getMessage() // For debugging, remove in production
            ], 500);
        }
    }

    /**
     * Display the specified speciality.
     *
     * Retrieves the details of a specific speciality by its UUID.
     * Only active specialities are returned.
     *
     * @urlParam speciality string required The UUID of the speciality. Example: "9a7c69a1-abcd-1234-9f88-2099d7c1bxyz"
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        try {
            // Validate that the ID is a UUID
            $validator = Validator::make(['id' => $id], [
                'id' => 'required|uuid',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid speciality ID format.',
                    'errors' => $validator->errors(),
                ], 400); // Bad Request
            }

            // Find the speciality by ID, ensuring it's active (status = 'ACTIVE')
            $speciality = Speciality::where('id', $id)
                                ->where('status', 'ACTIVE')
                                ->first(); // Fetches all columns by default

            if (!$speciality) {
                return response()->json([
                    'success' => false,
                    'message' => 'Speciality not found or is not active.',
                ], 404); // Not Found
            }

            // Return a successful response with the speciality data
            return response()->json([
                'success' => true,
                'message' => 'Speciality details retrieved successfully.',
                'data' => $speciality,
            ], 200);

        } catch (\Exception $e) {
            // \Log::error('Error fetching speciality details for ID ' . $id . ': ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching speciality details.',
                // 'error_detail' => $e->getMessage() // For debugging, remove in production
            ], 500);
        }
    }
}
