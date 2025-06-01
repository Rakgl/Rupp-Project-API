<?php

namespace App\Http\Controllers\Api\V1\Mobile\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserProfileController extends Controller
{
	public function getUserProfile()
	{
		$customer = auth()->user()->customer;

		return response()->json([
			'success' => true,
			'data' => [
				'phone' => $customer->phone,
				'name' => $customer->name,
				'email' => $customer->email,
			]
		]);
	}

	public function updateUserProfile(Request $request) 
	{
		$customer = auth()->user()->customer;
		$customer->update([
			'name' => $request->name,
			'email' => $request->email,
		]);

		return response()->json([
			'success' => true,
			'message' => 'Profile updated successfully',
			'data' => $customer
		]);
	}
}
