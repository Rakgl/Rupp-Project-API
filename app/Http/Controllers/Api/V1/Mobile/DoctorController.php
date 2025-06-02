<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Doctor; // Assuming your Doctor model is in App\Models
use App\Models\User;   // Assuming your User model is in App\Models for related data
// It's good practice to also import Speciality if you're referencing it often, though not strictly necessary for ::class usage
use App\Models\Speciality;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB; // For more complex queries if needed

/**
 * @group Doctors
 *
 * APIs for managing doctors (Mobile)
 */
class DoctorController extends Controller
{
    /**
     * Display a listing of the doctors.
     *
     * Retrieves a paginated list of active doctors.
     * Allows searching by name, registration number and filtering by hospital and speciality.
     *
     * @queryParam page int The page number to retrieve. Example: 1
     * @queryParam per_page int The number of items per page. Example: 15
     * @queryParam search string A search term to filter doctors by name (from users table) or registration number. Example: "John Doe" or "MD12345"
     * @queryParam hospital_id string Filter doctors by hospital UUID. Example: "9a7c69a1-..."
     * @queryParam is_available_for_consultation boolean Filter by availability. Example: true
     * @queryParam speciality_id string Filter doctors by speciality UUID. Example: "9a7c69a1-..."
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'per_page' => 'integer|min:1|max:100',
            'search' => 'string|nullable|max:255',
            'hospital_id' => 'nullable|uuid',
            'is_available_for_consultation' => 'nullable|boolean',
            'speciality_id' => 'nullable|uuid', // For filtering by speciality
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $perPage = $request->input('per_page', 15);

            $query = Doctor::query()
                ->with([
                    'user:id,name,email', // Select specific columns from users table
                    'hospital:id,name,city', // Select specific columns from hospitals table
                    'specialities:id,name' // Eager load specialities for the list view
                ])
                ->where('doctors.status', 'ACTIVE'); // Alias doctors table for clarity

            // Search functionality
            if ($request->filled('search')) {
                $searchTerm = '%' . $request->input('search') . '%';
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('doctors.registration_number', 'like', $searchTerm)
                      ->orWhere('doctors.bio', 'like', $searchTerm)
                      ->orWhereHas('user', function ($userQuery) use ($searchTerm) {
                          $userQuery->where('name', 'like', $searchTerm)
                                    ->orWhere('email', 'like', $searchTerm);
                      });
                });
            }

            // Filter by hospital_id
            if ($request->filled('hospital_id')) {
                $query->where('doctors.hospital_id', $request->input('hospital_id'));
            }

            // Filter by availability
            if ($request->has('is_available_for_consultation') && !is_null($request->input('is_available_for_consultation'))) {
                $isAvailable = filter_var($request->input('is_available_for_consultation'), FILTER_VALIDATE_BOOLEAN);
                $query->where('doctors.is_available_for_consultation', $isAvailable);
            }

            // Filter by speciality_id (assuming a many-to-many relationship 'specialities')
            // This filters the doctors list to only those who have the specified speciality.
            if ($request->filled('speciality_id')) {
                $query->whereHas('specialities', function ($specialityQuery) use ($request) {
                    $specialityQuery->where('specialities.id', $request->input('speciality_id'));
                });
            }

            // Select specific columns from the doctors table
            // Related data (user, hospital, specialities) is handled by the 'with' clause
            $doctors = $query->select(
                'doctors.id',
                'doctors.user_id',
                'doctors.title',
                'doctors.registration_number',
                'doctors.bio', // Consider if bio is needed for list view
                'doctors.gender',
                'doctors.consultation_fee',
                'doctors.currency_code',
                'doctors.years_of_experience',
                'doctors.profile_picture_path',
                'doctors.is_available_for_consultation',
                'doctors.availability_status',
                'doctors.average_rating',
                'doctors.hospital_id'
                // 'doctors.qualifications' // Might be too large for list view, better for 'show' method
            )
            ->orderByDesc('doctors.is_available_for_consultation') // Prioritize available doctors
            ->orderBy('doctors.average_rating', 'desc') // Then by rating
            ->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Doctors retrieved successfully.',
                'data' => $doctors,
            ], 200);

        } catch (\Exception $e) {
            // \Log::error('Error fetching doctors: ' . $e->getMessage() . ' Stack: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching doctors.',
                'error_detail' => $e->getMessage() // For debugging
            ], 500);
        }
    }

    /**
     * Display the specified doctor.
     *
     * Retrieves the details of a specific doctor by their UUID.
     * Only active doctors are returned.
     *
     * @urlParam doctor string required The UUID of the doctor. Example: "9a7c69a1-..."
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        try {
            $validator = Validator::make(['id' => $id], ['id' => 'required|uuid']);
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid doctor ID format.',
                    'errors' => $validator->errors(),
                ], 400);
            }

            $doctor = Doctor::with([
				'user:id,name,email', // Select specific columns from users table
				'hospital:id,name,city', // Select specific columns from hospitals table
				'specialities:id,name' // Eager load specialities for the list view
			])
            ->where('doctors.id', $id)
            ->where('doctors.status', 'ACTIVE')
            ->first(); // Use firstOrFail to throw an exception if not found, or handle null as is

            if (!$doctor) {
                return response()->json([
                    'success' => false,
                    'message' => 'Doctor not found or is not active.',
                ], 404);
            }

            // The 'qualifications' field is JSON, it will be handled automatically by Laravel's casting
            // if set up in the Doctor model (e.g., protected $casts = ['qualifications' => 'array'];)
            // If not cast, it will be returned as a JSON string.
            // You can decode it manually if needed, but model casting is preferred:
            // if ($doctor->qualifications && is_string($doctor->qualifications)) {
            //    $doctor->qualifications = json_decode($doctor->qualifications, true);
            // }


            return response()->json([
                'success' => true,
                'message' => 'Doctor details retrieved successfully.',
                'data' => $doctor,
            ], 200);

        } catch (\Exception $e) {
            // \Log::error('Error fetching doctor details for ID ' . $id . ': ' . $e->getMessage() . ' Stack: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching doctor details.',
                // 'error_detail' => $e->getMessage() // For debugging
            ], 500);
        }
    }
}
