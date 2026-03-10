<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Resources\Api\V1\Mobile\Product\ProductListResource;
use App\Http\Resources\Api\V1\Mobile\Product\ProductShowResource;

class ProductController extends Controller
{
    /**
     * Display a listing of products for the Mobile Home feed.
     */
    public function index(Request $request)
    {
        $query = Product::where('status', 'ACTIVE');

        // Allow mobile to filter by category ID (for the "Dogs" / "Cats" chips)
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Allow searching (for the search bar)
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                // Search in JSON column for product name
                $q->where('name->en', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('name->kh', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('sku', 'LIKE', '%' . $searchTerm . '%');
            });
        }

        $products = $query->with('category')
                          ->orderBy('created_at', 'desc')
                          ->paginate($request->get('limit', 15));

        return ProductListResource::collection($products);
    }

    /**
     * Display the specified product for the Mobile Detail view.
     */
    public function show(Product $product)
    {
        if ($product->status !== 'ACTIVE') {
            return response()->json(['message' => 'Product not available'], 404);
        }
        
        $product->load('category');
        return new ProductShowResource($product);
    }
}
