<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\Mobile\PaymentMethodResource;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
	public function index(Request $request)
	{
		$data = PaymentMethod::active()
					->orderBy('created_at', 'desc')
					->paginate($request->per_page);

		$resource = PaymentMethodResource::collection($data)->response()->getData(true);

		return response()->json([
			'success' => true,
			'message' => 'Payment methods retrieved successfully',
			'data' => $resource['data'],
			'meta' => [
				'current_page' => $resource['meta']['current_page'],
				'last_page' => $resource['meta']['last_page'],
				'total' => $resource['meta']['total'],
			]
		]);
	}
}
