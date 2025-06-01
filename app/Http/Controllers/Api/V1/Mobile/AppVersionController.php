<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\Mobile\AppVersion\AppVersionResource;
use App\Models\AppVersion;
use Illuminate\Http\Request;

class AppVersionController extends Controller
{
	public function version(Request $request)
	{
		if(!$request->platform) {
			return $this->errorResponse('Platform is required');
		}
		
		$appVersion = AppVersion::where('platform', $request->platform)
			->latest('created_at')
			->first();

		$data = $appVersion ? AppVersionResource::make($appVersion)->resolve() : null;

		return $this->successResponse($data);
	}

	protected function successResponse($data)
	{
		return response()->json([
			'success' => true,
			'message' => 'Success',
			'data' => $data,
		]);
	}

	protected function errorResponse($message)
	{
		return response()->json([
			'success' => false,
			'message' => $message,
			'data' => null,
		]);
	}
}
