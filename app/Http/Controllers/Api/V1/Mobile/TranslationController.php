<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\Mobile\TranslationResource;
use App\Models\Translation;
use Illuminate\Http\Request;

class TranslationController extends Controller
{
	public function getTranslations(Request $request)
    {
		$translations = Translation::where('platform', 'MOBILE')
				->get();
		$resource = TranslationResource::collection($translations);
		return response()->json([
			'success' => true,
			'message' => 'success',
			'data' => $resource
		]);
    }

}
