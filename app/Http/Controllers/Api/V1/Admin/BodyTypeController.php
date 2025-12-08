<?php
namespace App\Http\Controllers\Api\V1\Admin;

use App\Helpers\AppHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\Admin\BodyType\BodyTypeResource;
use App\Models\BodyType;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BodyTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $bodyTypes = BodyType::query()
            ->when($search, function ($query, $search) {
                $searchTerm = strtolower($search);
                $query->whereRaw('LOWER(name) like ?', ["%{$searchTerm}%"]);
            })
            ->latest()
            ->paginate(10);

        return BodyTypeResource::collection($bodyTypes);
    }

    public function all()
    {
        $bodyTypes = BodyType::orderBy('name')->get(['id', 'name']);

        return BodyTypeResource::collection($bodyTypes);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'name' => 'required|string|max:255|unique:body_types,name',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            if ($request->hasFile('image')) {
                $imagePath = AppHelper::uploadImage($request->file('image'), 'uploads/body-types');
                $data['image_url'] = $imagePath;
                unset($data['image']);
            }

            $bodyType = BodyType::create($data);

            return new BodyTypeResource($bodyType);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to create body type.', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(BodyType $bodyType)
    {
        $bodyType->load('cars');

        return new BodyTypeResource($bodyType);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BodyType $bodyType)
    {
        try {
            $data = $request->validate([
                'name' => 'required|string|max:255|unique:body_types,name,' . $bodyType->id,
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            if ($request->hasFile('image')) {
                if ($bodyType->image_url) {
                    Storage::delete($bodyType->image_url);
                }

                $imagePath = AppHelper::uploadImage($request->file('image'), 'uploads/body-types');
                $data['image_url'] = $imagePath;
                unset($data['image']);
            }

            $bodyType->update($data);

            return new BodyTypeResource($bodyType);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to update body type.', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BodyType $bodyType)
    {
        try {
            if ($bodyType->cars()->count() > 0) {
                return response()->json(['error' => 'Cannot delete body type with associated cars.'], 400);
            }

            if ($bodyType->image_url) {
                Storage::delete($bodyType->image_url);
            }

            $bodyType->delete();

            return response()->noContent();
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete body type.', 'message' => $e->getMessage()], 500);
        }
    }
}
