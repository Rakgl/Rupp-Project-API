<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Pet;
use Illuminate\Http\Request;

class PetController extends Controller
{
    /**
     * Display a listing of pets.
     * Optional filters: ?category_id=<uuid>, ?search=<string>
     */
    public function index(Request $request)
    {
        $query = Pet::with(['category']);

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                  ->orWhere('species', 'like', "%{$term}%")
                  ->orWhere('breed', 'like', "%{$term}%");
            });
        }

        $pets = $query->latest()->paginate($request->get('per_page', 10));

        return response()->json($pets);
    }

    /**
     * Display the specified pet.
     */
    public function show(Pet $pet)
    {
        $pet->load(['category']);
        return response()->json($pet);
    }
}
