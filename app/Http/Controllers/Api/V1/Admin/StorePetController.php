<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pet;
use App\Models\Store;
use Illuminate\Http\Request;

class StorePetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Store $store)
    {
        $pets = $store->pets()->latest()->paginate(10);
        return response()->json($pets);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Store $store)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'species' => 'required|string|max:255',
            'breed' => 'required|string|max:255',
            'weight' => 'nullable|numeric',
            'date_of_birth' => 'nullable|date',
            'image_url' => 'nullable|url',
            'medical_notes' => 'nullable|string',
        ]);

        $pet = $store->pets()->create($request->all());

        return response()->json($pet, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Pet $pet)
    {
        return response()->json($pet);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pet $pet)
    {
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'species' => 'sometimes|required|string|max:255',
            'breed' => 'sometimes|required|string|max:255',
            'weight' => 'nullable|numeric',
            'date_of_birth' => 'nullable|date',
            'image_url' => 'nullable|url',
            'medical_notes' => 'nullable|string',
        ]);

        $pet->update($request->all());

        return response()->json($pet);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pet $pet)
    {
        $pet->delete();

        return response()->json(null, 204);
    }
}
