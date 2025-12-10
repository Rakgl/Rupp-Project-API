<?php
namespace App\Http\Controllers\Api\V1\Admin;

use App\Helpers\AppHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\Admin\Car\CarResource;
use App\Http\Requests\Api\V1\Admin\Car\StoreCarRequest;
use App\Http\Requests\Api\V1\Admin\Car\UpdateCarRequest;
use App\Models\Car;
use App\Models\CarImage;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $cars = Car::with([
                'model:id,name,brand_id',
                'model.brand:id,name,image_url',
                'bodyType:id,name',
                'images',
            ])
            ->withCount('images')
            ->when($search, function ($query, $search) {
                $searchTerm = strtolower($search);
                $query->where(function ($q) use ($searchTerm) {
                    $q->whereHas('model', function ($q2) use ($searchTerm) {
                        $q2->whereRaw('LOWER(name) like ?', ["%{$searchTerm}%"])
                            ->orWhereHas('brand', function ($brandQuery) use ($searchTerm) {
                                $brandQuery->whereRaw('LOWER(name) like ?', ["%{$searchTerm}%"]);
                            });
                    })->orWhereHas('bodyType', function ($bodyTypeQuery) use ($searchTerm) {
                        $bodyTypeQuery->whereRaw('LOWER(name) like ?', ["%{$searchTerm}%"]);
                    })->orWhere('year', 'like', "%{$searchTerm}%");
                });
            })
            ->latest()
            ->paginate($request->input('per_page', 10));

        return CarResource::collection($cars);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCarRequest $request)
    {
        $data = $request->validated();

        try {
            DB::beginTransaction();

            $car = Car::create(collect($data)->except(['images', 'primary_image_index'])->all());

            $this->uploadImages(
                $car,
                $request->file('images', []),
                $request->input('primary_image_index')
            );

            DB::commit();

            $car->load([
                'model:id,name,brand_id',
                'model.brand:id,name,image_url',
                'bodyType:id,name',
                'images',
            ]);

            return new CarResource($car);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to create car', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['error' => 'Failed to create car.', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Car $car)
    {
        $car->load([
            'model:id,name,brand_id',
            'model.brand:id,name,image_url',
            'bodyType:id,name',
            'images',
        ]);

        return new CarResource($car);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCarRequest $request, Car $car)
    {
        $data = $request->validated();

        try {
            DB::beginTransaction();

            $car->update(collect($data)->except([
                'images',
                'primary_image_index',
                'primary_image_id',
                'remove_image_ids',
            ])->all());

            // Remove selected images
            if (!empty($data['remove_image_ids'])) {
                $imagesToRemove = CarImage::where('car_id', $car->id)
                    ->whereIn('id', $data['remove_image_ids'])
                    ->get();

                foreach ($imagesToRemove as $image) {
                    Storage::delete($image->image_path);
                    $image->delete();
                }
            }

            // Upload new images if provided
            $this->uploadImages(
                $car,
                $request->file('images', []),
                $request->input('primary_image_index'),
                true
            );

            // Set primary image if explicitly provided
            if (!empty($data['primary_image_id'])) {
                $this->setPrimaryImage($car, $data['primary_image_id']);
            }

            $this->ensurePrimaryImageExists($car);

            DB::commit();

            $car->load([
                'model:id,name,brand_id',
                'model.brand:id,name,image_url',
                'bodyType:id,name',
                'images',
            ]);

            return new CarResource($car);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to update car', [
                'car_id' => $car->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Failed to update car.', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Car $car)
    {
        try {
            $car->load('images');

            foreach ($car->images as $image) {
                Storage::delete($image->image_path);
            }

            $car->delete();

            return response()->noContent();
        } catch (Exception $e) {
            Log::error('Failed to delete car', [
                'car_id' => $car->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Failed to delete car.', 'message' => $e->getMessage()], 500);
        }
    }

    private function uploadImages(Car $car, array $images, ?int $primaryIndex = null, bool $preserveExistingPrimary = false): void
    {
        if (empty($images)) {
            return;
        }

        $uploadedImages = [];

        foreach ($images as $index => $image) {
            $imagePath = AppHelper::uploadImage($image, 'uploads/cars');

            $uploadedImages[] = CarImage::create([
                'car_id' => $car->id,
                'image_path' => $imagePath,
                'is_primary' => false,
            ]);
        }

        $hasPrimary = $car->images()->where('is_primary', true)->exists();

        if ($primaryIndex !== null && isset($uploadedImages[$primaryIndex])) {
            $this->setPrimaryImage($car, $uploadedImages[$primaryIndex]->id);
            return;
        }

        // Respect an existing primary image if requested
        if ($preserveExistingPrimary && $hasPrimary) {
            return;
        }

        if (!$hasPrimary && isset($uploadedImages[0])) {
            $this->setPrimaryImage($car, $uploadedImages[0]->id);
        }
    }

    private function setPrimaryImage(Car $car, string $imageId): void
    {
        if (!CarImage::where('car_id', $car->id)->where('id', $imageId)->exists()) {
            return;
        }

        CarImage::where('car_id', $car->id)->update(['is_primary' => false]);
        CarImage::where('car_id', $car->id)->where('id', $imageId)->update(['is_primary' => true]);
    }

    private function ensurePrimaryImageExists(Car $car): void
    {
        if ($car->images()->where('is_primary', true)->exists()) {
            return;
        }

        $firstImage = $car->images()->first();
        if ($firstImage) {
            $this->setPrimaryImage($car, $firstImage->id);
        }
    }
}
