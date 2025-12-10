<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\Review\StoreReviewRequest;
use App\Http\Requests\Api\V1\Admin\Review\UpdateReviewRequest;
use App\Http\Resources\Api\V1\Admin\Review\ReviewResource;
use App\Models\Review;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ReviewController extends Controller
{
    /**
     * Display a listing of the authenticated user's reviews.
     */
    public function index(Request $request)
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized.'], 401);
        }

        $ratingInput = $request->input('rating');
        $rating = is_numeric($ratingInput) ? (int) $ratingInput : null;
        $modelId = $request->input('model_id');

        $reviews = $user->reviews()
            ->with([
                'model:id,name,brand_id',
                'model.brand:id,name,image_url',
            ])
            ->when($rating !== null, fn($query) => $query->where('rating', $rating))
            ->when($modelId, function ($query, $modelId) {
                $query->where('model_id', $modelId);
            })
            ->latest()
            ->paginate($request->input('per_page', 10));

        return ReviewResource::collection($reviews);
    }

    /**
     * Store a newly created review.
     */
    public function store(StoreReviewRequest $request)
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized.'], 401);
        }

        $data = $request->validated();

        try {
            $review = Review::create([
                'model_id' => $data['model_id'],
                'user_id' => $user->id,
                'rating' => $data['rating'],
                'comment' => $data['comment'],
            ]);

            $review->load([
                'model:id,name,brand_id',
                'model.brand:id,name,image_url',
                'user:id,name,email',
            ]);

            return new ReviewResource($review);
        } catch (Exception $e) {
            Log::error('Failed to create review', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error' => 'Failed to create review.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified review.
     */
    public function show(Review $review)
    {
        if ($review->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized access to review.'], 403);
        }

        $review->load([
            'model:id,name,brand_id',
            'model.brand:id,name,image_url',
            'user:id,name,email',
        ]);

        return new ReviewResource($review);
    }

    /**
     * Update the specified review.
     */
    public function update(UpdateReviewRequest $request, Review $review)
    {
        if ($review->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized.'], 403);
        }

        $data = $request->validated();

        try {
            $review->update([
                'model_id' => $data['model_id'],
                'rating' => $data['rating'],
                'comment' => $data['comment'],
            ]);

            $review->load([
                'model:id,name,brand_id',
                'model.brand:id,name,image_url',
                'user:id,name,email',
            ]);

            return new ReviewResource($review);
        } catch (Exception $e) {
            Log::error('Failed to update review', [
                'review_id' => $review->id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error' => 'Failed to update review.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified review.
     */
    public function destroy(Review $review)
    {
        if ($review->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized.'], 403);
        }

        try {
            $review->delete();
            return response()->noContent();
        } catch (Exception $e) {
            Log::error('Failed to delete review', [
                'review_id' => $review->id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error' => 'Failed to delete review.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
