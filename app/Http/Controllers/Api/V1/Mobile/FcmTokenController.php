<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class FcmTokenController extends Controller
{
    public function store(Request $request)
	{
		$user = auth()->user()->id;
		$user = User::find($user);
		$platform = $request->platform;

		$user->update([
			'fcm_token' => $request->fcm_token,
			'platform' => $platform
		]);

		return response()->json([
			'success' => true,
			'message' => 'Fcm token updated successfully.',
			'data' => $request->fcm_token
		]);
	}
}
