<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Pharmacy; // Assuming your Pharmacy model is in App\Models
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule; // For boolean validation

/**
 * @group Pharmacies
 *
 * APIs for managing pharmacies (Mobile)
 */
class PharmacyController extends Controller
{
    /**
     * Display a listing of the pharmacies.
     *
     * Retrieves a paginated list of active pharmacies.
     *
     * @queryParam page int The page number to retrieve. Example: 1
     * @queryParam per_page int The number of items per page. Example: 15
     * @queryParam search string A search term to filter pharmacies by name, address, or city. Example: "Central"
     * @queryParam city string Filter pharmacies by city. Example: "Phnom Penh"
     * @queryParam country string Filter pharmacies by country. Example: "Cambodia"
     * @queryParam is_24_hours boolean Filter pharmacies that are open 24 hours. Example: true
     * @queryParam delivers_medication boolean Filter pharmacies that deliver medication. Example: true
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        // Validate incoming request parameters
        $validator = Validator::make($request->all(), [
            'per_page' => 'integer|min:1|max:100',
            'search' => 'string|nullable|max:255',
            'city' => 'string|nullable|max:100',
            'country' => 'string|nullable|max:100',
            'is_24_hours' => 'nullable|boolean', // Accepts true, false, 1, 0
            'delivers_medication' => 'nullable|boolean', // Accepts true, false, 1, 0
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
            // Assuming 'status' being true means active, as per migration default(true)
            $query = Pharmacy::query()->where('status', true);

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

            // Apply is_24_hours filter if provided
            if ($request->has('is_24_hours') && !is_null($request->input('is_24_hours'))) {
                 // Convert to boolean as query params can be strings 'true'/'false'
                $is24Hours = filter_var($request->input('is_24_hours'), FILTER_VALIDATE_BOOLEAN);
                $query->where('is_24_hours', $is24Hours);
            }

            // Apply delivers_medication filter if provided
            if ($request->has('delivers_medication') && !is_null($request->input('delivers_medication'))) {
                $deliversMedication = filter_var($request->input('delivers_medication'), FILTER_VALIDATE_BOOLEAN);
                $query->where('delivers_medication', $deliversMedication);
            }

            // Select specific columns to return for efficiency
            $pharmacies = $query->select(
                'id',
                'name',
                'address',
                'city',
                'state',
                'zip_code',
                'country',
                'phone_number',
                'email',
                'license_number',
                'opening_time',
                'closing_time',
                'is_24_hours',
                'delivers_medication',
                'delivery_details'
                // 'status' // Usually not needed in client response if already filtered
            )
            ->orderBy('name', 'asc') // Default ordering by name
            ->paginate($perPage);

            // Return a successful response
            return response()->json([
                'success' => true,
                'message' => 'Pharmacies retrieved successfully.',
                'data' => $pharmacies,
            ], 200);

        } catch (\Exception $e) {
            // \Log::error('Error fetching pharmacies: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching pharmacies.',
                // 'error_detail' => $e->getMessage() // For debugging, remove in production
            ], 500);
        }
    }

    /**
     * Display the specified pharmacy.
     *
     * Retrieves the details of a specific pharmacy by its UUID.
     * Only active pharmacies are returned.
     *
     * @urlParam pharmacy string required The UUID of the pharmacy. Example: "9a7c69a1-1234-5678-9f88-2099d7c1babc"
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
                    'message' => 'Invalid pharmacy ID format.',
                    'errors' => $validator->errors(),
                ], 400); // Bad Request
            }

            // Find the pharmacy by ID, ensuring it's active (status = true)
            $pharmacy = Pharmacy::where('id', $id)
                                ->where('status', true)
                                ->first();

            if (!$pharmacy) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pharmacy not found or is not active.',
                ], 404); // Not Found
            }

            // Return a successful response with the pharmacy data
            return response()->json([
                'success' => true,
                'message' => 'Pharmacy details retrieved successfully.',
                'data' => $pharmacy,
            ], 200);

        } catch (\Exception $e) {
            // \Log::error('Error fetching pharmacy details for ID ' . $id . ': ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching pharmacy details.',
                // 'error_detail' => $e->getMessage() // For debugging, remove in production
            ], 500);
        }
    }
}
