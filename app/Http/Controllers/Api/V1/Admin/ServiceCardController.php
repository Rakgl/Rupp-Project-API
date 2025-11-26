<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Helpers\AppHelper;
use App\Models\ServiceCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\Admin\ServiceCard\ServiceCardIndexResource;
use App\Http\Resources\Api\V1\Admin\ServiceCard\ServiceCardShowResource;

class ServiceCardController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = ServiceCard::query();

            if ($request->filled('search')) {
                $searchTerm = $request->input('search');
                // FIXED: PostgreSQL JSON Search syntax (using ILIKE for case-insensitive)
                $query->whereRaw("title->>'en' ILIKE ?", ['%' . $searchTerm . '%']);
            }
            
            $serviceCards = $query->latest()->paginate($request->input('per_page', 15));
            
            // Return collection resource
            return ServiceCardIndexResource::collection($serviceCards);

        } catch (\Exception $e) {
            Log::error('Error retrieving service cards: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving service cards.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        // REMOVED: button_link from validation
        $validator = Validator::make($request->all(), [
            'title' => 'required|array',
            'title.en' => 'required|string|max:255',
            'title.km' => 'nullable|string|max:255',
            'title.zh' => 'nullable|string|max:255',
            'description' => 'required|array',
            'description.en' => 'required|string',
            'description.km' => 'nullable|string',
            'description.zh' => 'nullable|string',
            'button_text' => 'required|array',
            'button_text.en' => 'required|string|max:100',
            'button_text.km' => 'nullable|string|max:100',
            'button_text.zh' => 'nullable|string|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation errors', 'errors' => $validator->errors()], 422);
        }

        try {
            $data = $validator->validated();
            
            if ($request->hasFile('image')) {
                // Ensure you have AppHelper imported
                $imagePath = AppHelper::uploadImage($request->file('image'), 'uploads/service_cards');
                $data['image_url'] = $imagePath;
            }
            
            unset($data['image']); // Remove file object before saving

            $data['created_by'] = auth()->id();
            $data['updated_by'] = auth()->id();

            $serviceCard = ServiceCard::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Service Card created successfully.',
                'data' => new ServiceCardShowResource($serviceCard)
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error creating service card: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the service card.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(ServiceCard $serviceCard)
    {
        return response()->json([
            'success' => true,
            'message' => 'Service Card details retrieved successfully.',
            'data' => new ServiceCardShowResource($serviceCard)
        ]);
    }

    public function update(Request $request, ServiceCard $serviceCard)
    {
        // REMOVED: button_link from validation
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|array',
            'title.en' => 'sometimes|required|string|max:255',
            'title.km' => 'nullable|string|max:255',
            'title.zh' => 'nullable|string|max:255',
            'description' => 'sometimes|required|array',
            'description.en' => 'sometimes|required|string',
            'description.km' => 'nullable|string',
            'description.zh' => 'nullable|string',
            'button_text' => 'sometimes|required|array',
            'button_text.en' => 'sometimes|required|string|max:100',
            'button_text.km' => 'nullable|string|max:100',
            'button_text.zh' => 'nullable|string|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'delete_image' => 'sometimes|boolean',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation errors', 'errors' => $validator->errors()], 422);
        }

        try {
            $data = $validator->validated();

            if ($request->hasFile('image')) {
                if ($serviceCard->image_url && Storage::exists($serviceCard->image_url)) {
                    Storage::delete($serviceCard->image_url);
                }
                $imagePath = AppHelper::uploadImage($request->file('image'), 'uploads/service_cards');
                $data['image_url'] = $imagePath;

            } elseif ($request->boolean('delete_image')) {
                if ($serviceCard->image_url && Storage::exists($serviceCard->image_url)) {
                    Storage::delete($serviceCard->image_url);
                }
                $data['image_url'] = null;
            }
            
            unset($data['image']);
            unset($data['delete_image']);

            $data['updated_by'] = auth()->id();

            $serviceCard->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Service Card updated successfully.',
                'data' => new ServiceCardShowResource($serviceCard->fresh())
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating service card: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'An error occurred.', 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy(ServiceCard $serviceCard)
    {
        try {
            if ($serviceCard->image_url && Storage::exists($serviceCard->image_url)) {
                Storage::delete($serviceCard->image_url);
            }

            $serviceCard->delete();

            return response()->json([
                'success' => true,
                'message' => 'Service Card deleted successfully.'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error deleting service card: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting the service card.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}