<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\Admin\Favorite\FavoriteIndexResource;
use App\Models\Favorite;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FavoriteController extends Controller
{
    /**
     * Return a paginated list of favorites.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Favorite::with(['user', 'favorable']);

            if ($request->has('user_id')) {
                $query->where('user_id', $request->input('user_id'));
            }

            if ($request->has('favorable_id')) {
                $query->where('favorable_id', $request->input('favorable_id'));
            }

            $favorites = $query->latest()->paginate($request->input('per_page', 10));
            $resource  = FavoriteIndexResource::collection($favorites)->response()->getData(true);

            return response()->json([
                'success' => true,
                'message' => 'Favorites retrieved successfully.',
                'data'    => $resource['data'],
                'meta'    => $resource['meta'] ?? null,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving favorites.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a favorite.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $favorite = Favorite::findOrFail($id);
            $favorite->delete();

            return response()->json([
                'success' => true,
                'message' => 'Favorite deleted successfully.',
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting favorite: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting the favorite.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
