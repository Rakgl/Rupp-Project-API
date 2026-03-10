<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Helpers\AppHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\Admin\Service\ServiceIndexResource;
use App\Http\Resources\Api\V1\Admin\Service\ServiceShowResource;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ServiceController extends Controller
{
    /**
     * Return a paginated list of services.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Service::query();

            // Search by name
            if ($request->has('search') && !empty($request->input('search'))) {
                $searchTerm = $request->input('search');
                $query->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($searchTerm) . '%']);
            }

            // Filter by status
            if ($request->has('status')) {
                $query->where('status', $request->input('status'));
            }

            $services = $query->latest()->paginate($request->input('per_page', 10));
            $resource   = ServiceIndexResource::collection($services)->response()->getData(true);

            return response()->json([
                'success' => true,
                'message' => 'Services retrieved successfully.',
                'data'    => $resource['data'],
                'meta'    => $resource['meta'] ?? null,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving services.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Return a single service by ID.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $service = Service::findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Service details retrieved successfully.',
                'data'    => new ServiceShowResource($service),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving service.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create a new service.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name'             => 'required|string|max:255',
            'description'      => 'nullable|string',
            'price'            => 'required|numeric|min:0',
            'duration_minutes' => 'required|integer|min:1',
            'image'            => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'status'           => 'required|in:ACTIVE,INACTIVE',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            $data = $validator->validated();

            $name          = $data['name'];
            $data['name']  = ['en' => $name, 'kh' => $name, 'zh' => $name];

            if (!empty($data['description'])) {
                $desc = $data['description'];
                $data['description'] = ['en' => $desc, 'kh' => $desc, 'zh' => $desc];
            } else {
                $data['description'] = null;
            }

            if ($request->hasFile('image')) {
                $data['image_url'] = AppHelper::uploadImage($request->file('image'), 'uploads/services');
            }
            unset($data['image']);

            $service = Service::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Service created successfully.',
                'data'    => new ServiceIndexResource($service),
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error creating service: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the service.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update an existing service.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $service = Service::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name'             => 'sometimes|required|string|max:255',
            'description'      => 'nullable|string',
            'price'            => 'sometimes|required|numeric|min:0',
            'duration_minutes' => 'sometimes|required|integer|min:1',
            'image'            => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'status'           => 'sometimes|required|in:ACTIVE,INACTIVE',
            'delete_image'     => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            $data = $validator->validated();

            if (isset($data['name'])) {
                $name         = $data['name'];
                $data['name'] = ['en' => $name, 'kh' => $name, 'zh' => $name];
            }

            if (array_key_exists('description', $data)) {
                if (!empty($data['description'])) {
                    $desc = $data['description'];
                    $data['description'] = ['en' => $desc, 'kh' => $desc, 'zh' => $desc];
                } else {
                    $data['description'] = null;
                }
            }

            // Handle image update or removal
            if ($request->hasFile('image')) {
                // Delete old image and upload new one
                if ($service->image_url && Storage::exists($service->image_url)) {
                    Storage::delete($service->image_url);
                }
                $data['image_url'] = AppHelper::uploadImage($request->file('image'), 'uploads/services');
            } elseif ($request->input('delete_image') == '1') {
                if ($service->image_url && Storage::exists($service->image_url)) {
                    Storage::delete($service->image_url);
                }
                $data['image_url'] = null;
            }

            // Unset temporary fields so they are not mass-assigned
            unset($data['image']);
            unset($data['delete_image']);

            $service->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Service updated successfully.',
                'data'    => new ServiceIndexResource($service->fresh()),
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating service: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the service.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a service.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $service = Service::findOrFail($id);

            // Delete associated image if exists
            if ($service->image_url && Storage::exists($service->image_url)) {
                Storage::delete($service->image_url);
            }

            $service->delete();

            return response()->json([
                'success' => true,
                'message' => 'Service deleted successfully.',
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting service: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting the service.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}