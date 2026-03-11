<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    /**
     * Display a listing of the user's favorite items.
     */
    public function index()
    {
        $favorites = Auth::user()->favorites()->with('favorable')->latest()->paginate(10);
        return response()->json($favorites);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'item_id' => 'required|uuid',
            'item_type' => 'required|in:product,pet_listing,pet,service',
        ]);

        $itemId = $request->input('item_id');
        $rawItemType = $request->input('item_type');

        $modelType = match($rawItemType) {
            'product' => \App\Models\Product::class,
            'pet_listing' => \App\Models\PetListing::class,
            'pet' => \App\Models\Pet::class,
            'service' => \App\Models\Service::class,
            default => \App\Models\Product::class,
        };

        // Ensure item exists
        $modelType::findOrFail($itemId);

        $favorite = Favorite::firstOrCreate([
            'user_id' => Auth::id(),
            'favorable_id' => $itemId,
            'favorable_type' => $modelType,
        ]);

        return response()->json($favorite, 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Favorite $favorite)
    {
        if ($favorite->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $favorite->delete();

        return response()->json(null, 204);
    }
}
