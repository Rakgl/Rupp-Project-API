<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\Helpers\OTPHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Api\V1\Mobile\ResendOTPRequest;
use App\Http\Requests\Api\V1\Mobile\VerifyOTPRequest;
use App\Http\Requests\Api\V1\Mobile\VerifyPhoneNumberRequest;
use App\Models\User;
use App\Models\Wallet;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        try {
            DB::beginTransaction();

            $phone = ltrim($request->phone, '0');
            
            $existingUser = User::where('phone', $phone)->first();
            
            if ($existingUser) {
                if ($existingUser->status !== 'DELETED') {
                    return response()->json([
                        'success' => false,
                        'message' => 'This phone number is already registered with an active account.',
                    ]);
                }
                
                if ($existingUser->updated_at->diffInDays(Carbon::now()) < 30) {
                    return response()->json([
                        'success' => false,
                        'message' => 'This account was recently deactivated. Please wait before registering again.',
                    ]);
                }
                
                $existingUser->update([
                    'name' => $request->name ?? 'User ' . $phone,
                    'email' => $request->email ?? null,
                    'password' => Hash::make($request->password),
                    'status' => 'ACTIVE',
                    'update_num' => $existingUser->update_num + 1,
                ]);

                $wallet = Wallet::firstOrCreate(
                    ['user_id' => $existingUser->id],
                    ['id' => (string) Str::uuid(), 'balance' => 0.00, 'status' => 'ACTIVE']
                );
                
                if ($wallet->status !== 'ACTIVE') {
                    $wallet->update(['status' => 'ACTIVE']);
                }

                $user = $existingUser;

            } else {
                $user = User::create([
                    'id' => (string) Str::uuid(),
                    'name' => $request->name ?? 'User ' . $phone,
                    'email' => $request->email ?? null,
                    'phone' => $phone,
                    'username' => $phone,
                    'password' => Hash::make($request->password),
                    'status' => 'ACTIVE',
                    'type' => 'Mobile',
                    'update_num' => 0
                ]);

                // Wallet::create([
                //     'id' => (string) Str::uuid(),
                //     'user_id' => $user->id,
                //     'balance' => 0.00,
                //     'status' => 'ACTIVE'
                // ]);
            }

            $dataAfterLogin = $this->registerLogin($user->username, $request->password);
            
            $userData = $user->toArray();
            $userData['access_token'] = $dataAfterLogin['access_token'] ?? null;
            $userData['refresh_token'] = $dataAfterLogin['refresh_token'] ?? null;
            
            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $userData,
                'message' => 'Account registered successfully.'
            ]);
            
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Registration failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function verifyPhoneNumber(VerifyPhoneNumberRequest $request) 
    {
        $phone = ltrim($request->phone, '0');
        $countryCode = $request->country_code;
        $len = $request->len ? (int) $request->len : 6;

        $isUserRegistered = User::where('phone', $phone)
                                ->where('status', 'ACTIVE')
                                ->first();

        if ($isUserRegistered) {
            return response()->json([
                'success' => false,
                'message' => 'Phone number already registered.',
            ]);
        } 
        
        $sendOtp = OTPHelper::send($phone, $len);
        
        if ($sendOtp['success']) {
            $userRegisteredOTP = UserRegisterOTP::create([
                'id' => (string) Str::uuid(),
                'transaction_code' => $sendOtp['data']['transaction_code'],
                'phone' => $phone,
                'country_code' => $countryCode,
                'status' => 'PENDING',
                'expired_at' => Carbon::now()->addMinute(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'OTP sent to your mobile number. Please verify.',
                'transaction_code' => $userRegisteredOTP->transaction_code,
                'expired_at' => $userRegisteredOTP->expired_at
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => $sendOtp['message'],
            ]);
        }
    }

    public function verifyOTP(VerifyOTPRequest $request) 
    {
        $otp = $request->otp;
        $transactionCode = $request->transaction_code;

        $verifyOTP = OTPHelper::verify(md5($otp . $transactionCode), $transactionCode);
        
        if ($verifyOTP && isset($verifyOTP['success']) && $verifyOTP['success'] === true) {
            $userRegisteredOTP = UserRegisterOTP::where('transaction_code', $transactionCode)->first();
            if ($userRegisteredOTP) {
                $userRegisteredOTP->update([
                    'attempts' => $userRegisteredOTP->attempts + 1,
                    'status' => 'VERIFIED',
                ]);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => $verifyOTP['message'] ?? 'Invalid OTP.',
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
        $resendOTP = OTPHelper::resend($transactionCode);

        if ($resendOTP && isset($resendOTP['success']) && $resendOTP['success'] === false) {
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

    public function registerLogin($username, $password)
    {
        $user = User::where('username', $username)
            ->where('status', 'ACTIVE')
            ->first();

        if (!$user || !Hash::check($password, $user->password)) {
            return [
                'access_token' => null,
                'refresh_token' => null,
            ];
        }

        return $this->getTokenAndRefreshToken($user);
    }

    public function getTokenAndRefreshToken($user)
    {
        $request = request();

        $requestIdentity = 'User: ' . $user->username .
            ', Device: ' . $request->header('User-Agent') .
            ', IP: ' . $request->ip();

        $expireSeconds = (int) env('SANCTUM_TOKEN_EXPIRATION', 7200);
        $refreshTokenExpiration = (int) env('SANCTUM_REFRESH_EXPIRATION', 604800);

        $refreshToken = Str::random(64);
        $refreshTokenId = (string) Str::uuid();

        DB::table('refresh_tokens')->insert([
            'id' => $refreshTokenId,
            'user_id' => $user->id,
            'name' => $requestIdentity,
            'token' => hash('sha256', $refreshToken),
            'expires_at' => Carbon::now()->addSeconds($refreshTokenExpiration),
            'revoked' => false,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $token = $user->createToken($refreshTokenId, ['mobile-user'], now()->addSeconds($expireSeconds));

        return [
            'access_token' => $token->plainTextToken,
            'token_type' => 'Bearer',
            'expires_in' => $expireSeconds,
            'refresh_token' => $refreshToken,
        ];
    }
}