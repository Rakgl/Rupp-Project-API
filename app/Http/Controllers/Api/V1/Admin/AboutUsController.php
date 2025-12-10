<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\AboutUs;
use App\Http\Resources\Api\V1\Admin\AboutUs\AboutUsIndexResource;
use App\Http\Resources\Api\V1\Admin\AboutUs\AboutUsShowResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AboutUsController extends Controller
{
    /**
     * Display a paginated listing of about us entries.
     */
    public function index(Request $request)
    {
        try {
            $query = AboutUs::query();
            if ($request->has('search') && !empty($request->input('search'))) {
                $searchTerm = strtolower($request->input('search'));
                $query->where(function($q) use ($searchTerm) {
                    $q->whereRaw('LOWER(title) LIKE ?', ["%{$searchTerm}%"]);
                });
            }

            $blocks = $query->latest()->paginate($request->input('per_page', 15));
            
            $resource = AboutUsIndexResource::collection($blocks)->response()->getData(true);

            return response()->json([
                'success' => true,
                'message' => 'About Us entries retrieved successfully.',
                'data' => $resource['data'],
                'meta' => $resource['meta'] ?? null
            ]);
        } catch (\Exception $e) {
            Log::error('Error retrieving about us entries: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving data.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request,AboutUs $aboutUs)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required', 
            'description' => 'nullable', 
            'list_text' => 'nullable',
            'status' => 'required|in:ACTIVE,INACTIVE,DELETED',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
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

            if ($request->hasFile('image')) {
                if ($aboutUs->image_path && Storage::disk('public')->exists($aboutUs->image_path)) {
                    Storage::disk('public')->delete($aboutUs->image_path);
                }
                $imagePath = $request->file('image')->store('content_blocks', 'public');
                $data['image_path'] = $imagePath;
            } elseif ($request->input('delete_image') == '1') {
                if ($aboutUs->image_path && Storage::disk('public')->exists($aboutUs->image_path)) {
                    Storage::disk('public')->delete($aboutUs->image_path);
                }
                $data['image_path'] = null;
            }
            
            unset($data['image']);
            unset($data['delete_image']);

            $aboutUs->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Created successfully.',
                'data' => new AboutUsShowResource($aboutUs)
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error creating about us: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $aboutUs = AboutUs::findOrFail($id);
            return response()->json([
                'success' => true,
                'message' => 'Retrieved successfully.',
                'data' => new AboutUsShowResource($aboutUs)
            ]);
        } catch (\Exception $e) {
             return response()->json(['success' => false, 'message' => 'Entry not found.'], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $aboutUs = AboutUs::find($id);
        
        if (!$aboutUs) {
             return response()->json(['success' => false, 'message' => 'Entry not found.'], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required',
            'description' => 'sometimes|nullable',
            'list_text' => 'sometimes|nullable',
            'status' => 'sometimes|required|in:ACTIVE,INACTIVE,DELETED',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'delete_image' => 'sometimes|boolean',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation errors', 'errors' => $validator->errors()], 422);
        }

        try {
            $data = $validator->validated();

            // Handle Image Upload
            if ($request->hasFile('image')) {
                if ($aboutUs->image && Storage::disk('public')->exists($aboutUs->image)) {
                    Storage::disk('public')->delete($aboutUs->image);
                }
                $data['image'] = $request->file('image')->store('about_us', 'public');

            } elseif ($request->input('delete_image') == '1') {
                if ($aboutUs->image && Storage::disk('public')->exists($aboutUs->image)) {
                    Storage::disk('public')->delete($aboutUs->image);
                }
                $data['image'] = null;
            }
            
            unset($data['delete_image']);

            $aboutUs->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Updated successfully.',
                'data' => new AboutUsShowResource($aboutUs->fresh())
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating about us: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'An error occurred while updating.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $aboutUs = AboutUs::findOrFail($id);
            if ($aboutUs->image && Storage::disk('public')->exists($aboutUs->image)) {
                Storage::disk('public')->delete($aboutUs->image);
            }

            $aboutUs->delete();

            return response()->json([
                'success' => true,
                'message' => 'Deleted successfully.'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error deleting about us: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}