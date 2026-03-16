<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\Http\Controllers\Controller;
use App\Models\PetListing;
use App\Http\Resources\Api\V1\Mobile\PetListing\PetListingResource;
use Illuminate\Http\Request;

class PetListingController extends Controller
{
    /**
     * Display a listing of pets for sale or adoption.
     */
    public function index(Request $request)
    {
        $query = PetListing::with(['pet.category'])
            ->where('status', 'AVAILABLE');

        if ($request->filled('type')) {
            $query->where('listing_type', strtoupper($request->type));
        }

        if ($request->filled('search')) {
            $term = $request->search;
            $query->whereHas('pet', function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                  ->orWhere('species', 'like', "%{$term}%")
                  ->orWhere('breed', 'like', "%{$term}%");
            });
        }

        $listings = $query->latest()->paginate($request->get('per_page', 10));

        return PetListingResource::collection($listings);
    }

    /**
     * Display the specified listing.
     */
    public function show(PetListing $petListing)
    {
        if ($petListing->status !== 'AVAILABLE') {
            return response()->json(['message' => 'Listing not found'], 404);
        }

        $petListing->load(['pet.category']);
        return new PetListingResource($petListing);
    }
}
