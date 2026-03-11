<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of active categories.
     * Optional filter: ?type=PRODUCT or ?type=PET
     */
    public function index(Request $request)
    {
        $query = Category::where('status', 'ACTIVE');

        if ($request->filled('type')) {
            $query->where('type', strtoupper($request->type));
        }

        $categories = $query->latest()->paginate($request->get('per_page', 10));

        return response()->json($categories);
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        if ($category->status !== 'ACTIVE') {
            return response()->json(['message' => 'Category not found'], 404);
        }
        return response()->json($category);
    }
}
