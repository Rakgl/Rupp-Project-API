<?php

namespace App\Helpers;

use App\Models\PasswordResetOTP;
use App\Models\UserRegisterOTP;
use Illuminate\Support\Facades\Http;

class OTPHelper
{
	public static function send($phone, $lengthOTP): \Illuminate\Http\JsonResponse|array
    {
		$phone = '855' . $phone;
		$url = env('MEKONG_SMS_URL');
		$pass = md5(env('MEKONG_SMS_PASS'));
        try {
            $response = Http::asForm()->post($url . '/otp/sendotp.aspx',[
                'user'=> env('MEKONG_SMS_USER'),
                'pass'=> $pass,
                'sender'=> env('MEKONG_SMS_SENDER'),
				'ph'=> $phone,
                'text1'=> env('MEKONG_SMS_TEXT1'),
                'text2'=> env('MEKONG_SMS_TEXT2'),
                'expiry'=> env('MEKONG_SMS_EXPIRY'),
                'len'=> $lengthOTP ? $lengthOTP : 4
            ])->body();
            return self::sendResponse($response);
        }
        catch (\Exception $exception){
            return [
                'success' => false,
                'message' => '',
                'data' => $exception,
            ];
        }
    }

	public static function resend($transactionCode, $lengthOTP = 6, $type = 'register'): \Illuminate\Http\JsonResponse|array
    {
		$url = env('MEKONG_SMS_URL');
		$pass = md5(env('MEKONG_SMS_PASS'));
        try {
            $response = Http::asForm()->post($url . '/otp/resendotp.aspx',[
                'user'=> env('MEKONG_SMS_USER'),
                'pass'=> $pass,
                'tc'=> $transactionCode,
                'len'=> $lengthOTP
            ])->body();

            $response= self::resendResponse($response);
			
			if($type == 'register') {
				$existTransaction = UserRegisterOTP::where('transaction_code',$transactionCode)->first();
				if ($response && $response['success'] === true && isset($response['data']) && isset($response['data']['transaction_code']) && $existTransaction->transaction !== $response['data']['transaction_code']) {
					$existTransaction->transaction_code = $response['data']['transaction_code'];
					$existTransaction->save();
				}
			} else if ($type = 'forget_password') { 
				$existTransaction = PasswordResetOTP::where('transaction_code', $transactionCode)->first();
				if ($response && $response['success'] === true && isset($response['data']) && isset($response['data']['transaction_code']) && $existTransaction->transaction !== $response['data']['transaction_code']) {
					$existTransaction->transaction_code = $response['data']['transaction_code'];
					$existTransaction->save();
				}
			}
            
            return $response;
        }
        catch (\Exception $exception){
            return [
                'success'=>false,
                'message'=>$exception,
                'data'=>$exception,
            ];
        }
    }

	public static function verify($otp, $transactionCode): \Illuminate\Http\JsonResponse|array
	{
		$url = env('MEKONG_SMS_URL');
		$pass = md5(env('MEKONG_SMS_PASS'));
		try {
            $response = Http::asForm()->post($url.'/otp/verifyotp.aspx',[
                'user'=> env('MEKONG_SMS_USER'),
                'pass'=> $pass,
                'tc'=> $transactionCode,
                'otp'=> $otp
            ])->body();
            return self::verifyResponse($response , $transactionCode);
        }
        catch (\Exception $exception){
            return [
                'success'=>false,
                'message'=>$exception,
                'data'=>$exception,
            ];
        }
	}

	protected static function sendResponse($res): array
    {
        $dataResponse= explode(':', $res, 2);
        return match ((int)$dataResponse[0]) {
            0 => [
                'success' => true,
                'message' => "OTP code have been sent to your mobile phone.",
                'data' => [
                    'transaction_code' => $dataResponse[1],
                ],
            ],
            3 => [
                'success' => false,
                'message' => "Your phone number or country code are invalid.",
                'data' => [
                    'error_code' => $dataResponse[0],
                    'error_message' => $dataResponse[1]
                ],
            ],
            default => [
                'success' => false,
                'message' => 'Your phone number or country code are invalid.',
                'data' => [
                    'error_code' => $dataResponse[0],
                    'error_message' => $dataResponse[1]
                ],
            ],
        };
    }
	
	protected static function verifyResponse($res, $transactionCode=null): array
    {
        $dataResponse= explode(':',$res,2);
        return match ((int)$dataResponse[0]) {
            0 => [
                'success' => true,
                'message' => "Your Verification code are verified.",
                'data' => [
                    'transaction_code'=>$transactionCode
                ],
            ],
            8 => [
                'success' => false,
                'message' => "Your verification code are incorrect.",
                'data' => [
                    'error_code' => $dataResponse[0],
                    'error_message' => $dataResponse[1]
                ],
            ],
            9 => [
                'success' => false,
                'message' => "Your verification code are expired.",
                'data' => [
                    'error_code' => $dataResponse[0],
                    'error_message' => $dataResponse[1]
                ],
            ],
            10 => [
                'success' => false,
                'message' => "Your verification code are verified.",
                'data' => [
                    'error_code' => $dataResponse[0],
                    'error_message' => $dataResponse[1]
                ],
            ],
            default => [
                'success' => false,
                'message' => 'Your verification\'s code are invalid.',
                'data' => [
                    'error_code' => $dataResponse[0],
                    'error_message' => $dataResponse[1]
                ],
            ],
        };
    }

	protected static function resendResponse($res): array
    {
        $dataResponse= explode(':',$res,2);
        return match ((int)$dataResponse[0]) {
            0 => [
                'success' => true,
                'message' => "OTP code have been sent to your mobile phone.",
                'data' => [
                    'transaction_code' => $dataResponse[1]
                ],
            ],
            10 => [
                'success' => false,
                'message' => "Your verification code are already verify.",
                'data' => [
                    'error_code' => $dataResponse[0],
                    'error_message' => $dataResponse[1]
                ],
            ],
            default => [
                'success' => false,
                'message' => 'Your phone number or country code are invalid.',
                'data' => [
                    'error_code' => $dataResponse[0],
                    'error_message' => $dataResponse[1]
                ],
            ],
        };
    }
}
?>