<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Helpers\Helper;

class SettingController extends Controller
{
    public function index()
	{
		$generalSettings = Setting::get()->pluck('setting_value', 'setting_key')->toArray();

		foreach ($generalSettings as $key => $value) {
			if ($key == 'app_logo') {
				$generalSettings[$key] = Helper::imageUrl($value);
			}
		}
		
		return response()->json([
			'success' => true,
			'data' => [
				'app_name' => $generalSettings['app_name'] ?? null,
				'color' => $generalSettings['color'] ?? null,
				'logo' => $generalSettings['app_logo'] ?? null,
                'about_us' => [
                    'description' => $generalSettings['about_us_description'] ?? null,
                    'location' => [
                        'latitude' => $generalSettings['latitude'] ?? null,
                        'longitude' => $generalSettings['longitude'] ?? null,
                    ],
                    'footer_note' => $generalSettings['footer_note'] ?? null,
                ]
			],
		]);
	}
}
