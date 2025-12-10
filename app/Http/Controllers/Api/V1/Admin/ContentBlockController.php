<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\Admin\ContentBlock\ContentBlockIndexResource;
use App\Http\Resources\Api\V1\Admin\ContentBlock\ContentBlockShowResource;
use App\Models\ContentBlock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ContentBlockController extends Controller
{
    /**
     * Display a paginated listing of content blocks.
     */
    public function index(Request $request)
    {
        try {
            $query = ContentBlock::query();

            if ($request->has('search') && !empty($request->input('search'))) {
                $searchTerm = strtolower($request->input('search'));
                // Changed from searching 'slug' to searching 'title'
                $query->where(function($q) use ($searchTerm) {
                    $q->whereRaw('LOWER(title) LIKE ?', ["%{$searchTerm}%"]);
                });
            }

            $blocks = $query->latest()->paginate($request->input('per_page', 15));
            $resource = ContentBlockIndexResource::collection($blocks)->response()->getData(true);

            return response()->json([
                'success' => true,
                'message' => 'Content blocks retrieved successfully.',
                'data' => $resource['data'],
                'meta' => $resource['meta'] ?? null
            ]);
        } catch (\Exception $e) {
            Log::error('Error retrieving content blocks: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving content blocks.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created content block in storage.
     */
    public function store(Request $request)
    {
        // REMOVED 'slug' from validation
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|array',
            'description' => 'sometimes|required|array',
            'booking_btn' => 'sometimes|required|array',
            'status' => 'sometimes|required|in:ACTIVE,INACTIVE,DELETED',
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
                $imagePath = $request->file('image')->store('content_blocks', 'public');
                $data['image_path'] = $imagePath;
            }
            unset($data['image']);

            $block = ContentBlock::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Content block created successfully.',
                'data' => $block
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error creating content block: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the content block.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified content block.
     */
    public function show(ContentBlock $contentBlock)
    {
        return response()->json([
            'success' => true,
            'message' => 'Content block details retrieved successfully.',
            'data' => new ContentBlockShowResource($contentBlock)
        ]);
    }

    /**
     * Update the specified content block in storage.
     */
    public function update(Request $request, ContentBlock $contentBlock)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|array',
            'description' => 'sometimes|required|array',
            'booking_btn' => 'sometimes|required|array',
            'status' => 'sometimes|required|in:ACTIVE,INACTIVE,DELETED',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'delete_image' => 'sometimes|boolean',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation errors', 'errors' => $validator->errors()], 422);
        }

        try {
            $data = $validator->validated();

            if ($request->hasFile('image')) {
                if ($contentBlock->image_path && Storage::disk('public')->exists($contentBlock->image_path)) {
                    Storage::disk('public')->delete($contentBlock->image_path);
                }
                $imagePath = $request->file('image')->store('content_blocks', 'public');
                $data['image_path'] = $imagePath;
            } elseif ($request->input('delete_image') == '1') {
                if ($contentBlock->image_path && Storage::disk('public')->exists($contentBlock->image_path)) {
                    Storage::disk('public')->delete($contentBlock->image_path);
                }
                $data['image_path'] = null;
            }
            
            unset($data['image']);
            unset($data['delete_image']);

            $contentBlock->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Content block updated successfully.',
                'data' => new ContentBlockShowResource($contentBlock->fresh())
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating content block: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'An error occurred while updating the content block.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified content block from storage.
     */
    public function destroy(ContentBlock $contentBlock)
    {
        try {
            if ($contentBlock->image_path && Storage::disk('public')->exists($contentBlock->image_path)) {
                Storage::disk('public')->delete($contentBlock->image_path);
            }

            $contentBlock->delete();

            return response()->json([
                'success' => true,
                'message' => 'Content block deleted successfully.'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error deleting content block: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting the content block.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}