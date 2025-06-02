<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Hospital; // Assuming your Hospital model is in App\Models
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

/**
 * @group Hospitals
 *
 * APIs for managing hospitals (Mobile)
 */
class HospitalController extends Controller
{
    /**
     * Display a listing of the hospitals.
     *
     * Retrieves a paginated list of active hospitals.
     *
     * @queryParam page int The page number to retrieve. Example: 1
     * @queryParam per_page int The number of items per page. Example: 15
     * @queryParam search string A search term to filter hospitals by name, city, or address. Example: "General"
     * @queryParam city string Filter hospitals by city. Example: "New York"
     * @queryParam country string Filter hospitals by country. Example: "USA"
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        // Validate incoming request parameters
        $request->validate([
            'per_page' => 'integer|min:1|max:100',
            'search' => 'string|nullable|max:255',
            'city' => 'string|nullable|max:100',
            'country' => 'string|nullable|max:100',
        ]);

        try {
            // Get the number of items per page, default to 15
            $perPage = $request->input('per_page', 15);

            // Start building the query
            $query = Hospital::query()->where('status', 'ACTIVE'); // Fetch only active hospitals

            // Apply search filter if provided
            if ($request->filled('search')) {
                $searchTerm = '%' . $request->input('search') . '%';
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('name', 'like', $searchTerm)
                      ->orWhere('address', 'like', $searchTerm)
                      ->orWhere('city', 'like', $searchTerm);
                });
            }

            // Apply city filter if provided
            if ($request->filled('city')) {
                $query->where('city', $request->input('city'));
            }

            // Apply country filter if provided
            if ($request->filled('country')) {
                $query->where('country', $request->input('country'));
            }

            // Select specific columns to return for efficiency
            // You can adjust these based on what the mobile app needs
            $hospitals = $query->select(
                'id',
                'name',
                'address',
                'city',
                'state',
                'zip_code',
                'country',
                'phone_number',
                'email',
                'website',
                'description' // Consider if description is needed for list view, can be large
            )
            ->orderBy('name', 'asc') // Default ordering by name
            ->paginate($perPage);

            // Return a successful response
            return response()->json([
                'success' => true,
                'message' => 'Hospitals retrieved successfully.',
                'data' => $hospitals,
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            // Handle any other errors
            // Log the error for debugging: \Log::error('Error fetching hospitals: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching hospitals.',
                'error' => $e->getMessage(), // Optionally include error message in dev
            ], 500);
        }
    }


	public function show(string $id): JsonResponse
    {
        try {
            $validator = Validator::make(['id' => $id], [
                'id' => 'required|uuid',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid hospital ID format.',
                    'errors' => $validator->errors(),
                ], 400); // Bad Request for invalid ID format
            }

            // Find the hospital by ID, ensuring it's active
            $hospital = Hospital::where('id', $id)
                                ->where('status', 'ACTIVE')
                                ->first();

            if (!$hospital) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hospital not found or is not active.',
                ], 404); // Not Found
            }

            // Return a successful response with the hospital data
            // You might want to select specific fields here as well, similar to the index method
            return response()->json([
                'success' => true,
                'message' => 'Hospital details retrieved successfully.',
                'data' => $hospital, // Or $hospital->toArray() or a Resource class
            ], 200);

        } catch (\Exception $e) {
            // Handle any other errors
            // \Log::error('Error fetching hospital details for ID ' . $id . ': ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching hospital details.',
                // 'error' => $e->getMessage(), // Be cautious in production
            ], 500);
        }
    }
}
