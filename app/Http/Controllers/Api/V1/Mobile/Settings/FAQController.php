<?php

namespace App\Http\Controllers\Api\V1\Mobile\Settings;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\Mobile\Settings\FAQ\FAQIndexResource;
use App\Models\Faq;
use Illuminate\Http\Request;

class FAQController extends Controller
{
	public function index(Request $request) {
		$data = Faq::active()
			->orderBy('created_at', 'desc')
			->paginate($request->per_page);

		$resource = FAQIndexResource::collection($data)->response()->getData(true);
		return response()->json([
			'data' => $resource['data'],
			'success' => true,
			'message' => 'Faq has found',
			'meta' => [
				'current_page' => $resource['meta']['current_page'],
				'last_page' => $resource['meta']['last_page'],
				'total' => $resource['meta']['total'],
			]
		]);
	}
}
