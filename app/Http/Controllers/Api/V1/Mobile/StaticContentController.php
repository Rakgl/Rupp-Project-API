<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\Mobile\StaticContentResource;
use App\Models\StaticContent;
use Illuminate\Http\Request;

class StaticContentController extends Controller
{
    public function privacyPolicies(Request $request)
    {
        $data = StaticContent::where('type', 'PRIVACY_POLICY')->active()->first();

		if(!$data){
			return response()->json([
				'success' => false,
				'message' => 'Privacy Policy not found',
				'data' => null
			]);
		}

        return response()->json([
            'success' => true,
            'message' => 'Privacy Policy retrieved successfully',
            'data' => new StaticContentResource($data)
        ]);
    }

    public function aboutUs(Request $request)
    {
        $data = StaticContent::where('type', 'ABOUT_US')->active()->first();

		if(!$data){
			return response()->json([
				'success' => false,
				'message' => 'About Us not found',
				'data' => null
			]);
		}

        return response()->json([
            'success' => true,
            'message' => 'About Us retrieved successfully',
			'data' => new StaticContentResource($data)
        ]);
    }
}
