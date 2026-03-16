<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Pet;
use App\Http\Resources\Api\V1\Mobile\Pet\PetResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PetController extends Controller
{
    /**
     * Display a listing of the user's pets.
     */
    public function index(Request $request)
    {
        $query = Pet::where('user_id', Auth::id())->with(['category']);

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

        return PetResource::collection($pets);
    }

    /**
     * Store a newly created pet in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'nullable|uuid|exists:categories,id',
            'name' => 'required|string|max:100',
            'species' => 'required|string|max:50',
            'breed' => 'nullable|string|max:100',
            'weight' => 'nullable|numeric|min:0',
            'date_of_birth' => 'nullable|date|before:today',
            'image' => 'nullable|image|max:2048', // 2MB max
            'medical_notes' => 'nullable|string|max:2000',
        ]);

        $data = $request->except('image');
        $data['user_id'] = Auth::id();

        if ($request->hasFile('image')) {
            // Basic Laravel upload logic
            $path = $request->file('image')->store('pets', 'public');
            $data['image_url'] = asset('storage/' . $path);
        }

        $pet = Pet::create($data);
        $pet->load('category');

        return (new PetResource($pet))->additional([
            'success' => true,
            'message' => 'Pet added successfully.'
        ]);
    }

    /**
     * Display the specified pet.
     */
    public function show(Pet $pet)
    {
        if ($pet->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $pet->load(['category']);
        return new PetResource($pet);
    }

    /**
     * Update the specified pet in storage.
     */
    public function update(Request $request, Pet $pet)
    {
        if ($pet->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'category_id' => 'nullable|uuid|exists:categories,id',
            'name' => 'sometimes|required|string|max:100',
            'species' => 'sometimes|required|string|max:50',
            'breed' => 'nullable|string|max:100',
            'weight' => 'nullable|numeric|min:0',
            'date_of_birth' => 'nullable|date|before:today',
            'image' => 'nullable|image|max:2048',
            'medical_notes' => 'nullable|string|max:2000',
        ]);

        $data = $request->except('image');

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('pets', 'public');
            $data['image_url'] = asset('storage/' . $path);
        }

        $pet->update($data);
        $pet->load('category');

        return (new PetResource($pet))->additional([
            'success' => true,
            'message' => 'Pet updated successfully.'
        ]);
    }

    /**
     * Remove the specified pet from storage.
     */
    public function destroy(Pet $pet)
    {
        if ($pet->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $pet->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pet removed successfully.'
        ]);
    }
}
