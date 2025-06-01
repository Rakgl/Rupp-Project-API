<?php

namespace App\Http\Controllers\Api\V1\Mobile;


use App\Helpers\OTPHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Mobile\ResendOTPRequest;
use App\Http\Requests\Api\V1\Mobile\ResetPasswordRequest;
use App\Http\Requests\Api\V1\Mobile\VerifyOTPRequest;
use App\Http\Requests\Api\V1\Mobile\VerifyPhoneNumberRequest;
use App\Models\Customer;
use App\Models\PasswordResetOTP;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\UserRegisterOTP;

class ForgetPasswordController extends Controller
{
	public function verifyPhoneNumber(VerifyPhoneNumberRequest $request) 
    { 
        $phone = ltrim($request->phone, '0');
		$phone = str_replace(' ', '', $phone);
        $countryCode = $request->country_code;
        $len = $request->len ? (int) $request->len : 6;

        $isCustomerExist = Customer::where('phone', $phone)
                            ->where('country_code', $countryCode)
                            ->active()
                            ->first();

        if(!$isCustomerExist) { 
            return response()->json([
                'success' => false,
                'message' => 'You don\'t have account yet. Please register first.',
            ]);
        } else {
            $sendOtp = OTPHelper::send($phone, $len);
            if($sendOtp['success']) {
                $passwordResetOTP = PasswordResetOTP::create([
                    'transaction_code' => $sendOtp['data']['transaction_code'],
                    'phone' => $phone,
                    'country_code' => $countryCode,
                    'status' => 'PENDING',
                    'expired_at' => Carbon::now()->addMinute(),
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $sendOtp['message'],
                ]);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'OTP sent to your mobile number. Please verify.',
                'transaction_code' => $passwordResetOTP->transaction_code,
                'expired_at' => $passwordResetOTP->expired_at
            ]);
        }
    }

	public function verifyOTP(VerifyOTPRequest $request) 
    {
        $otp = $request->otp;
        $transactionCode = $request->transaction_code;

        $verifyOTP = OTPHelper::verify(md5($otp. $transactionCode), $transactionCode);
        if ($verifyOTP && isset($verifyOTP['success']) && $verifyOTP['success'] === true) {
            $passwordResetOTP = PasswordResetOTP::where('transaction_code', $transactionCode)->first();
            if($passwordResetOTP) {
                $passwordResetOTP->update([
                    'attempts' => $passwordResetOTP->attempts + 1,
                    'status' => 'VERIFIED',
                ]);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => $verifyOTP['message'],
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'OTP verified successfully.',
            'transaction_code' => $transactionCode
        ]);
    }

	public function resendOTP(ResendOTPRequest $request) 
    {
        $transactionCode = $request->transaction_code;
        $resendOTP = OTPHelper::resend($transactionCode, 6, 'forget_password');

        if($resendOTP && isset($resendOTP['success']) && $resendOTP['success'] === false) {
            return response()->json([
                'success' => false,
                'message' => $resendOTP['message'],
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'OTP resend successfully.',
            'transaction_code' => $resendOTP['data']['transaction_code']
        ]);
    }

	public function resetPassword(ResetPasswordRequest $request)
	{	
		$newPassword = $request->new_password;
		$confirmPassword = $request->confirm_password;

		if($newPassword != $confirmPassword) {
			return response()->json([
				'success' => false,
				'message' => 'New password and confirm password does not match.',
			]);
		}
		$phone = ltrim($request->phone, '0');
		$countryCode = $request->country_code;

		$customer = Customer::where('phone', $phone)
							->where('country_code', $countryCode)
							->active()
							->first();

		if(!$customer) {
			return response()->json([
				'success' => false,
				'message' => 'Customer not found.',
			]);
		}
		$user = User::where('customer_id', $customer->id)->first();
		$user->update([
			'password' => Hash::make($newPassword),
		]);

		return response()->json([
			'success' => true,
			'message' => 'Password reset successfully. Please login with new password.',
		]);
	}
}
