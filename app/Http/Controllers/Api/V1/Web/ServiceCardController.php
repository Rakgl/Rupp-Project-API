<?php

namespace App\Http\Controllers\api\v1\web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ServiceCard;
use App\Http\Resources\Api\V1\Admin\ServiceCard\ServiceCardIndexResource;
use App\Http\Resources\Api\V1\Admin\ServiceCard\ServiceCardShowResource;

class ServiceCardController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = ServiceCard::query();

            if ($request->has('search') && !empty($request->input('search'))) {
                $searchTerm = strtolower($request->input('search'));
                $query->whereRaw('LOWER(JSON_EXTRACT(title, "$.en")) LIKE ?', ['%' . $searchTerm . '%']);
            }
            
            $serviceCards = $query->latest()->paginate($request->input('per_page', 15));
            
            $resource = ServiceCardIndexResource::collection($serviceCards)->response()->getData(true);

            return response()->json([
                'success' => true,
                'message' => 'Service Cards retrieved successfully.',
                'data' => $resource['data'],
                'meta' => $resource['meta'] ?? null
            ]);
        } catch (\Exception $e) {
            Log::error('Error retrieving service cards: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving service cards.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}