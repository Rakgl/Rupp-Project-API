<?php

namespace App\Http\Controllers\Api\V1\web;

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

            // Handle the search parameter (by slug)
            if ($request->has('search') && !empty($request->input('search'))) {
                $searchTerm = $request->input('search');
                $query->whereRaw('LOWER(slug) LIKE ?', ['%' . strtolower($searchTerm) . '%']);
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
}