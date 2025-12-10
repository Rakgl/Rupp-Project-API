<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Helpers\AppHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\Admin\UserListing\UserListingResource;
use App\Http\Requests\Api\V1\Admin\UserListing\StoreUserListingRequest;
use App\Http\Requests\Api\V1\Admin\UserListing\UpdateUserListingRequest;
use App\Models\UserListing;
use App\Models\UserListingImage;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UserListingController extends Controller
{
    /**
     * Display a listing of the authenticated user's listings.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $condition = $request->input('condition');

        /** @var User|null $user */
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized.'], 401);
        }

        $listings = $user->listings()
            ->with([
                'model:id,name,brand_id',
                'model.brand:id,name,image_url',
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
                    })
                        ->orWhere('year', 'like', "%{$searchTerm}%")
                        ->orWhere('condition', 'like', "%{$searchTerm}%")
                        ->orWhereRaw('LOWER(description) like ?', ["%{$searchTerm}%"]);
                });
            })
            ->when($status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($condition, function ($query, $condition) {
                $query->where('condition', $condition);
            })
            ->latest()
            ->paginate($request->input('per_page', 10));

        return UserListingResource::collection($listings);
    }

    /**
     * Store a newly created listing in storage.
     */
    public function store(StoreUserListingRequest $request)
    {
        $data = $request->validated();

        try {
            DB::beginTransaction();

            $listing = UserListing::create([
                'user_id' => Auth::id(),
                'model_id' => $data['model_id'],
                'year' => $data['year'],
                'condition' => $data['condition'],
                'price' => $data['price'],
                'description' => $data['description'],
                'status' => 'Pending',
            ]);

            $this->uploadImages(
                $listing,
                $request->file('images', []),
                $request->input('primary_image_index')
            );

            DB::commit();

            $listing->load([
                'model:id,name,brand_id',
                'model.brand:id,name,image_url',
                'images',
            ]);

            return new UserListingResource($listing);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to create user listing', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'error' => 'Failed to create listing.',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified listing.
     */
    public function show(UserListing $listing)
    {
        // Users can only view their own listings or approved listings
        if ($listing->user_id !== Auth::id() && $listing->status !== 'approved') {
            return response()->json(['error' => 'Unauthorized access to listing.'], 403);
        }

        $listing->load([
            'model:id,name,brand_id',
            'model.brand:id,name,image_url',
            'images',
            'user:id,name,email',
        ]);

        return new UserListingResource($listing);
    }

    /**
     * Update the specified listing in storage.
     */
    public function update(UpdateUserListingRequest $request, UserListing $listing)
    {
        // Authorization check
        if ($listing->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized.'], 403);
        }

        // Can't edit approved or sold listings
        if (in_array($listing->status, ['approved', 'sold'])) {
            return response()->json([
                'error' => 'Cannot edit listings that are approved or sold.',
                'message' => 'Please contact support for assistance.'
            ], 422);
        }

        $data = $request->validated();

        try {
            DB::beginTransaction();

            // Persist provided status. Previously the status was always
            // overwritten to 'pending' after any edit which prevented
            // clients from updating the status. Keep provided value.
            $listing->update([
                'model_id' => $data['model_id'],
                'year' => $data['year'],
                'condition' => $data['condition'],
                'status' => $data['status'],
                'price' => $data['price'],
                'description' => $data['description'],
            ]);

            // Remove selected images
            if (!empty($data['remove_image_ids'])) {
                $imagesToRemove = UserListingImage::where('user_listing_id', $listing->id)
                    ->whereIn('id', $data['remove_image_ids'])
                    ->get();

                foreach ($imagesToRemove as $image) {
                    Storage::delete($image->image_path);
                    $image->delete();
                }
            }

            // Upload new images if provided
            $this->uploadImages(
                $listing,
                $request->file('images', []),
                $request->input('primary_image_index'),
                true
            );

            // Set primary image if explicitly provided
            if (!empty($data['primary_image_id'])) {
                $this->setPrimaryImage($listing, $data['primary_image_id']);
            }

            $this->ensurePrimaryImageExists($listing);

            // Ensure at least one image exists
            if ($listing->images()->count() === 0) {
                DB::rollBack();
                return response()->json([
                    'error' => 'Listing must have at least one image.'
                ], 422);
            }

            DB::commit();

            $listing->load([
                'model:id,name,brand_id',
                'model.brand:id,name,image_url',
                'images',
            ]);

            return new UserListingResource($listing);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to update user listing', [
                'listing_id' => $listing->id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'error' => 'Failed to update listing.',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified listing from storage.
     */
    public function destroy(UserListing $listing)
    {
        if ($listing->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized.'], 403);
        }

        try {
            $listing->load('images');

            foreach ($listing->images as $image) {
                Storage::delete($image->image_path);
            }

            $listing->delete();

            return response()->noContent();
        } catch (Exception $e) {
            Log::error('Failed to delete user listing', [
                'listing_id' => $listing->id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'error' => 'Failed to delete listing.',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark listing as sold.
     */
    public function markAsSold(UserListing $listing)
    {
        if ($listing->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized.'], 403);
        }

        if ($listing->status !== 'approved') {
            return response()->json([
                'error' => 'Only approved listings can be marked as sold.'
            ], 422);
        }

        $listing->update(['status' => 'sold']);

        $listing->load([
            'model:id,name,brand_id',
            'model.brand:id,name,image_url',
            'images',
        ]);

        return new UserListingResource($listing);
    }

    /**
     * Request listing reactivation.
     */
    public function requestReactivation(UserListing $listing)
    {
        if ($listing->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized.'], 403);
        }

        if (!in_array($listing->status, ['rejected', 'expired'])) {
            return response()->json([
                'error' => 'Only rejected or expired listings can be reactivated.'
            ], 422);
        }

        $listing->update(['status' => 'pending']);

        $listing->load([
            'model:id,name,brand_id',
            'model.brand:id,name,image_url',
            'images',
        ]);

        return new UserListingResource($listing);
    }

    /**
     * Upload images for the listing.
     */
    private function uploadImages(
        UserListing $listing,
        array $images,
        ?int $primaryIndex = null,
        bool $preserveExistingPrimary = false
    ): void {
        if (empty($images)) {
            return;
        }

        $uploadedImages = [];

        foreach ($images as $index => $image) {
            $imagePath = AppHelper::uploadImage($image, 'uploads/user-listings');

            $uploadedImages[] = UserListingImage::create([
                'user_listing_id' => $listing->id,
                'image_path' => $imagePath,
                'is_primary' => false,
            ]);
        }

        $hasPrimary = $listing->images()->where('is_primary', true)->exists();

        if ($primaryIndex !== null && isset($uploadedImages[$primaryIndex])) {
            $this->setPrimaryImage($listing, $uploadedImages[$primaryIndex]->id);
            return;
        }

        // Respect an existing primary image if requested
        if ($preserveExistingPrimary && $hasPrimary) {
            return;
        }

        if (!$hasPrimary && isset($uploadedImages[0])) {
            $this->setPrimaryImage($listing, $uploadedImages[0]->id);
        }
    }

    /**
     * Set the primary image for the listing.
     */
    private function setPrimaryImage(UserListing $listing, string $imageId): void
    {
        if (!UserListingImage::where('user_listing_id', $listing->id)->where('id', $imageId)->exists()) {
            return;
        }

        UserListingImage::where('user_listing_id', $listing->id)->update(['is_primary' => false]);
        UserListingImage::where('user_listing_id', $listing->id)->where('id', $imageId)->update(['is_primary' => true]);
    }

    /**
     * Ensure at least one primary image exists.
     */
    private function ensurePrimaryImageExists(UserListing $listing): void
    {
        if ($listing->images()->where('is_primary', true)->exists()) {
            return;
        }

        $firstImage = $listing->images()->first();
        if ($firstImage) {
            $this->setPrimaryImage($listing, $firstImage->id);
        }
    }
}
