<?php

namespace App\Http\Controllers\Api\V1\Mobile\Settings;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ChangePasswordController extends Controller
{
	public function verifyCurrentPassword(Request $request) 
	{
		$user = auth()->user();

		if(password_verify($request->current_password, $user->password)) {
			return response()->json([
				'success' => true,
				'message' => 'Your Current password is correct.',
			]);
		}
		return response()->json([
			'success' => false,
			'message' => 'Your Current password is not correct.',
		]);
	}


	public function changePassword(Request $request) 
	{
		$newPassword = $request->new_password;
		$confirmNewPassword = $request->confirm_new_password;
		$userId = auth()->user()->id;

		if($newPassword != $confirmNewPassword) {
			return response()->json([
				'success' => false,
				'message' => 'Your new password and confirm password does not match.',
			]);
		}
		$user = User::where('id' , $userId)->first();
		$user->update([
			'password' => Hash::make($newPassword),
		]);

		return response()->json([
			'success' => true,
			'message' => 'Your password has been changed successfully.',
		]);
	}
}
