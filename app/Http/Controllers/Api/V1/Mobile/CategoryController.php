<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $categories = Category::where('status', 'ACTIVE')->latest()->paginate(10);

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
