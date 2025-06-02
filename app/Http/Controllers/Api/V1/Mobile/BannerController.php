<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;

/**
 * @group Banners
 *
 * APIs for retrieving banners (Mobile)
 */
class BannerController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'location' => 'string|nullable|max:255',
            'lang' => 'string|nullable|max:10',
            'region' => 'string|nullable|max:10',
            'per_page' => 'integer|min:1|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $perPage = $request->input('per_page', 10);
            $now = Carbon::now();

            $query = Banner::query()
                ->where('status', 'ACTIVE')
                ->where(function ($q) use ($now) {
                    $q->whereNull('start_date')
                      ->orWhere('start_date', '<=', $now);
                })
                ->where(function ($q) use ($now) {
                    $q->whereNull('end_date')
                      ->orWhere('end_date', '>=', $now);
                });

            if ($request->filled('location')) {
                $location = $request->input('location');
                $query->where(function ($q) use ($location) {
                    $q->where('display_locations', 'LIKE', "%{$location}%");
                });
            }

            if ($request->filled('lang')) {
                $query->where(function ($q) use ($request) {
                    $q->where('language_code', $request->input('lang'))
                      ->orWhereNull('language_code')
                      ->orWhere('language_code', '');
                });
            }

            if ($request->filled('region')) {
                $query->where(function ($q) use ($request) {
                    $q->where('region_code', $request->input('region'))
                      ->orWhereNull('region_code')
                      ->orWhere('region_code', '');
                });
            }

            $banners = $query->select(
                'id',
                'name',
                'image_url_mobile',
                'image_url_tablet',
                'title_text',
                'subtitle_text',
                'cta_text',
                'cta_action_type',
                'cta_action_value',
                'priority'
            )
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Banners retrieved successfully.',
                'data' => $banners,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching banners.',
				'errors' => $e->getMessage(),
            ], 500);
        }
    }

    public function recordClick(string $bannerId): JsonResponse
    {
        try {
            $validator = Validator::make(['id' => $bannerId], ['id' => 'required|uuid|exists:banners,id']);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid banner ID.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $banner = Banner::find($bannerId);
            if ($banner) {
                $banner->increment('click_count');
                return response()->json(['success' => true, 'message' => 'Banner click recorded.'], 200);
            }
            return response()->json(['success' => false, 'message' => 'Banner not found.'], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while recording the click.',
            ], 500);
        }
    }

    public function recordImpressions(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'banner_ids' => 'required|array|min:1',
            'banner_ids.*' => 'required|uuid|exists:banners,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid data provided for impressions.',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $bannerIds = $request->input('banner_ids');
            Banner::whereIn('id', $bannerIds)->increment('impression_count');

            return response()->json(['success' => true, 'message' => 'Banner impressions recorded.'], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while recording impressions.',
            ], 500);
        }
    }
}
