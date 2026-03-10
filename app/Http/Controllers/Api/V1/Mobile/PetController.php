<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Pet;
use Illuminate\Http\Request;

class PetController extends Controller
{
    /**
     * Display a listing of pets.
     */
    public function index(Request $request)
    {
        $pets = Pet::query()
            ->when($request->store_id, function ($query, $storeId) {
                return $query->where('store_id', $storeId);
            })
            ->latest()
            ->paginate(10);

        return response()->json($pets);
    }

    /**
     * Display the specified pet.
     */
    public function show(Pet $pet)
    {
        return response()->json($pet);
    }
}
