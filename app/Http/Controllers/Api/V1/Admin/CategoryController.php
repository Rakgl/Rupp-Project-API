<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Helpers\AppHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\Admin\Category\CategoryIndexResource;
use App\Http\Resources\Api\V1\Admin\Category\CategoryShowResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Return a paginated list of categories.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Category::query();

            // Search by name
            if ($request->has('search') && !empty($request->input('search'))) {
                $searchTerm = $request->input('search');
                $query->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($searchTerm) . '%']);
            }

            // Filter by status
            if ($request->has('status')) {
                $query->where('status', $request->input('status'));
            }

            $categories = $query->latest()->paginate($request->input('per_page', 10));
            $resource   = CategoryIndexResource::collection($categories)->response()->getData(true);

            return response()->json([
                'success' => true,
                'message' => 'Categories retrieved successfully.',
                'data'    => $resource['data'],
                'meta'    => $resource['meta'] ?? null,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving categories.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Return a single category by ID.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $category = Category::findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Category details retrieved successfully.',
                'data'    => new CategoryShowResource($category),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving category.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create a new category.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name'      => 'required|string|max:255',
            'slug'      => 'nullable|string|unique:categories,slug|max:255',
            'image'     => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'status'    => 'required|in:ACTIVE,INACTIVE',
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
            $data['slug']  = $data['slug'] ?? Str::slug($name);
            if ($request->hasFile('image')) {
                $data['image_url'] = AppHelper::uploadImage($request->file('image'), 'uploads/categories');
            }
            unset($data['image']);

            $category = Category::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Category created successfully.',
                'data'    => new CategoryIndexResource($category),
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error creating category: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the category.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update an existing category.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $category = Category::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name'         => 'sometimes|required|string|max:255',
            'slug'         => 'nullable|string|unique:categories,slug,' . $id . '|max:255',
            'image'        => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'status'       => 'sometimes|required|in:ACTIVE,INACTIVE',
            'delete_image' => 'sometimes|boolean',
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

            // Wrap plain string name into JSON structure for all locales
            if (isset($data['name'])) {
                $name         = $data['name'];
                $data['name'] = ['en' => $name, 'kh' => $name, 'zh' => $name];

                // Auto-update slug from name if slug is not provided
                if (empty($data['slug'])) {
                    $data['slug'] = Str::slug($name);
                }
            }

            // Handle image update or removal
            if ($request->hasFile('image')) {
                // Delete old image and upload new one
                if ($category->image_url && Storage::exists($category->image_url)) {
                    Storage::delete($category->image_url);
                }
                $data['image_url'] = AppHelper::uploadImage($request->file('image'), 'uploads/categories');
            } elseif ($request->input('delete_image') == '1') {
                if ($category->image_url && Storage::exists($category->image_url)) {
                    Storage::delete($category->image_url);
                }
                $data['image_url'] = null;
            }

            // Unset temporary fields so they are not mass-assigned
            unset($data['image']);
            unset($data['delete_image']);

            $category->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Category updated successfully.',
                'data'    => new CategoryIndexResource($category->fresh()),
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating category: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the category.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a category.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $category = Category::findOrFail($id);

            // Delete associated image if exists
            if ($category->image_url && Storage::exists($category->image_url)) {
                Storage::delete($category->image_url);
            }

            $category->delete();

            return response()->json([
                'success' => true,
                'message' => 'Category deleted successfully.',
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting category: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting the category.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
