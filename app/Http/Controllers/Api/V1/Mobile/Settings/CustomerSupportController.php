<?php

namespace App\Http\Controllers\Api\V1\Mobile\Settings;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\Mobile\Settings\CustomerSupport\CustomerSupportIndexResource;
use App\Models\CustomerSupport;
use Illuminate\Http\Request;

class CustomerSupportController extends Controller
{
	public function index(Request $request)
	{
		$data = CustomerSupport::active()
					->orderBy('display_order', 'asc')->paginate($request->per_page);

		$resource = CustomerSupportIndexResource::collection($data)->response()->getData(true);

		return response()->json([
			'success' => true,
			'message' => 'Customer support retrieved successfully',
			'data' => $resource['data'],
			'meta' => [
				'current_page' => $resource['meta']['current_page'],
				'last_page' => $resource['meta']['last_page'],
				'total' => $resource['meta']['total'],
			]
		]);
	}
}
