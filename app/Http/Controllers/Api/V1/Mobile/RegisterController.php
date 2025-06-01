<?php
namespace App\Http\Controllers\Api\V1\Mobile;

use App\Helpers\CustomerHelper;
use App\Helpers\Helper;
use App\Helpers\OTPHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Api\V1\Mobile\ResendOTPRequest;
use App\Http\Requests\Api\V1\Mobile\VerifyOTPRequest;
use App\Http\Requests\Api\V1\Mobile\VerifyPhoneNumberRequest;
use App\Models\User;
use App\Models\Customer;
use App\Models\CustomerVehicle;
use App\Models\IdTag;
use App\Models\UserRegisterOTP;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Notifications\TelegramRegistrationNotification;
use Illuminate\Support\Facades\Log;

class RegisterController extends Controller
{	
	// public function register(Request $request)
    // {
	// 	try {
	// 		DB::beginTransaction();
	// 		$phone = ltrim($request->phone, '0');
	// 		$findExistingCustomer = Customer::where('phone', $phone)
	// 										->where('country_code', $request->country_code)
	// 										->first();
	// 		if($findExistingCustomer) {
	// 			$findExistingCustomer->update([
	// 				'name' => $request->name,
	// 				'email' => $request->email ? $request->email : null,
	// 				'country_code' => $request->country_code,
	// 				'phone' => $phone,
	// 				'status' => 'ACTIVE',
	// 				'created_by' => null,
	// 				'updated_by' => null,
	// 				'update_num' => $findExistingCustomer->update_num + 1,
	// 				'points' => $findExistingCustomer->points,
	// 				'balance' => $findExistingCustomer->balance
	// 			]);

	// 			CustomerHelper::updateCustomerMembership($findExistingCustomer->id);

	// 			$defaultVehicle = CustomerVehicle::where('customer_id', $findExistingCustomer->id)
	// 											->where('is_default', true)
	// 											->first();
	// 			$defaultVehicle->update([
	// 				'brand_id' => $request->brand_id,
	// 				'model_id' => $request->model_id,
	// 				'color_id' => $request->color_id,
	// 				'year_id' => $request->year_id,
	// 				'plate' => $request->plate,
	// 				'is_default' => true,
	// 				'customer_id' => $findExistingCustomer->id,
	// 				'status' => 'ACTIVE',
	// 			]);

	// 			$user = User::where('customer_id' ,$findExistingCustomer->id)->first();
	// 			$user->update([
	// 				'name' => $phone,
	// 				'email' => $request->email ? $request->email : null,
	// 				'image' => null,
	// 				'username' => $phone,
	// 				'password' => Hash::make($request->password),
	// 				'customer_id' => $findExistingCustomer->id,
	// 				'status' => 'ACTIVE',
	// 				'created_by' => null,
	// 				'updated_by' => null,
	// 				'update_num' => $user->update_num + 1,
	// 			]);


	// 			$user->notify(new TelegramRegistrationNotification($user));

	// 			$idTag = IdTag::where('entity_id', $user->id)->first();
	// 			if(!$idTag) {
	// 				IdTag::create([
	// 					'entity_id' => $user->id,
	// 					'type' => 'Customer',
	// 					'status' => 'ACTIVE'
	// 				]);
	// 			}

	// 			$idTag->update([
	// 				'entity_id' => $user->id,
	// 				'type' => 'Customer',
	// 				'status' => 'ACTIVE'
	// 			]);


	// 			$accessToken = null;
	// 			$refreshToken = null;
	// 			if($findExistingCustomer && $defaultVehicle && $user) {
	// 				$dataAfterLogin = $this->registerLogin($user->username, $request->password);
	// 				$accessToken = isset($dataAfterLogin['access_token']) ? $dataAfterLogin['access_token'] : null;
	// 				$refreshToken = isset($dataAfterLogin['refresh_token']) ? $dataAfterLogin['refresh_token'] : null;
	// 			}
	// 			$findExistingCustomer['access_token'] = $accessToken;
	// 			$findExistingCustomer['refresh_token'] = $refreshToken;

			

	// 			return response()->json([
	// 				'success' => true,
	// 				'data' => $findExistingCustomer,
	// 				'message' => 'Customer registered successfully.',
	// 			]);
	// 		}

	// 		$customer = Customer::create([
	// 			'name' => $request->name,
	// 			'email' => $request->email ? $request->email : null,
	// 			'country_code' => $request->country_code,
	// 			'phone' => $phone,
	// 			'status' => 'ACTIVE',
	// 			'created_by' => null,
	// 			'updated_by' => null,
	// 			'update_num' => 0,
	// 			'points' => 0.00,
	// 			'balance' => 0,
	// 			'first_login_at' => Carbon::now(),
	// 		]);

	// 		CustomerHelper::updateCustomerMembership($customer->id);
	// 		$bonusMessage = CustomerHelper::getBonusBalance($customer);

	// 		$defaultVehicle = Helper::defaultVehicle();
	// 		$customerVehicle = CustomerVehicle::create([
	// 			'brand_id' => $request->brand_id ? $request->brand_id : $defaultVehicle['brand_id'],
	// 			'model_id' => $request->model_id ? $request->model_id : $defaultVehicle['model_id'],
	// 			'color_id' => $request->color_id ? $request->color_id : $defaultVehicle['color_id'],
	// 			'year_id' => $request->year_id ? $request->year_id : $defaultVehicle['year_id'],
	// 			'plate' => $request->plate,
	// 			'is_default' => true,
	// 			'customer_id' => $customer->id,
	// 			'status' => 'ACTIVE',
	// 		]);

	// 		$user = User::create([
	// 			'name' => $phone,
	// 			'email' => $request->email ? $request->email : null,
	// 			'image' => null,
	// 			'username' => $phone,
	// 			'password' => Hash::make($request->password),
	// 			'customer_id' => $customer->id,
	// 			'status' => 'ACTIVE',
	// 			'created_by' => null,
	// 			'updated_by' => null,
	// 			'update_num' => 0
	// 		]);

	// 		IdTag::create([
	// 			'entity_id' => $user->id,
	// 			'type' => 'Customer',
	// 			'status' => 'ACTIVE'
	// 		]);

	// 		$accessToken = null;
	// 		$refreshToken = null;
	// 		if($customer && $customerVehicle && $user) {
	// 			$dataAfterLogin = $this->registerLogin($user->username, $request->password);
	// 			$accessToken = isset($dataAfterLogin['access_token']) ? $dataAfterLogin['access_token'] : null;
	// 			$refreshToken = isset($dataAfterLogin['refresh_token']) ? $dataAfterLogin['refresh_token'] : null;
	// 		}
	// 		$customer['access_token'] = $accessToken;
	// 		$customer['refresh_token'] = $refreshToken;
			
	// 		$user->notify(new TelegramRegistrationNotification($user));

	// 		DB::commit();


	// 		return response()->json([
	// 			'success' => true,
	// 			'data' => $customer,
	// 			'message' => 'Customer registered successfully.',
	// 			'bonus_message' => $bonusMessage,
	// 			'has_received_bonus' => $customer->has_received_bonus
	// 		]);
	// 	} catch (Exception $e) {
	// 		DB::rollBack();
	// 		return response()->json([
	// 			'success' => false,
	// 			'message' => $e->getMessage(),
	// 		]);
	// 	}
    // }

	public function register(Request $request)
	{
		try {
			DB::beginTransaction();
			$phone = ltrim($request->phone, '0');
			
			// Check for existing customer by phone
			$findExistingCustomer = Customer::where('phone', $phone)
										->where('country_code', $request->country_code)
										->first();
			
			if($findExistingCustomer) {
				// Check if the customer has a "DELETED" status
				if ($findExistingCustomer->status !== 'DELETED') {
					// If customer exists and is not deleted, return a message
					return response()->json([
						'success' => false,
						'message' => 'This phone number is already registered with an active account.',
					]);
				}
				
				// Check if deleted account is in cooldown period
				if ($findExistingCustomer->updated_at->diffInDays(Carbon::now()) < 30) {
					return response()->json([
						'success' => false,
						'message' => 'This account was recently deactivated. Please wait before registering again.',
					]);
				}
				
				// Only reach here if customer exists, has "DELETED" status, and cooldown period has passed
				$findExistingCustomer->update([
					'name' => $request->name,
					'email' => $request->email ? $request->email : null,
					'country_code' => $request->country_code,
					'phone' => $phone,
					'status' => 'ACTIVE',
					'created_by' => null,
					'updated_by' => null,
					'update_num' => $findExistingCustomer->update_num + 1,
					// Preserve points/balance when reactivating
					'points' => $findExistingCustomer->points,
					'balance' => $findExistingCustomer->balance,
					// Reset the bonus flag
					'has_received_bonus' => false
				]);

				CustomerHelper::updateCustomerMembership($findExistingCustomer->id);

				// Handle default vehicle
				$defaultVehicle = CustomerVehicle::where('customer_id', $findExistingCustomer->id)
										->where('is_default', true)
										->first();
										
				if ($defaultVehicle) {
					$defaultVehicle->update([
						'brand_id' => $request->brand_id,
						'model_id' => $request->model_id,
						'color_id' => $request->color_id,
						'year_id' => $request->year_id,
						'plate' => $request->plate,
						'is_default' => true,
						'customer_id' => $findExistingCustomer->id,
						'status' => 'ACTIVE',
					]);
				} else {
					// Create a default vehicle if none exists
					$defaultVehicle = Helper::defaultVehicle();
					$defaultVehicle = CustomerVehicle::create([
						'brand_id' => $request->brand_id ? $request->brand_id : $defaultVehicle['brand_id'],
						'model_id' => $request->model_id ? $request->model_id : $defaultVehicle['model_id'],
						'color_id' => $request->color_id ? $request->color_id : $defaultVehicle['color_id'],
						'year_id' => $request->year_id ? $request->year_id : $defaultVehicle['year_id'],
						'plate' => $request->plate,
						'is_default' => true,
						'customer_id' => $findExistingCustomer->id,
						'status' => 'ACTIVE',
					]);
				}

				// Handle user account
				$user = User::where('customer_id', $findExistingCustomer->id)->first();
		
				if ($user) {
					$user->update([
						'name' => $phone,
						'email' => $request->email ? $request->email : null,
						'image' => null,
						'username' => $phone,
						'password' => Hash::make($request->password),
						'customer_id' => $findExistingCustomer->id,
						'status' => 'ACTIVE',
						'created_by' => null,
						'updated_by' => null,
						'update_num' => $user->update_num + 1,
					]);
				} else {
					// Create user if none exists
					$user = User::create([
						'name' => $phone,
						'email' => $request->email ? $request->email : null,
						'image' => null,
						'username' => $phone,
						'password' => Hash::make($request->password),
						'customer_id' => $findExistingCustomer->id,
						'status' => 'ACTIVE',
						'created_by' => null,
						'updated_by' => null,
						'update_num' => 0
					]);
				}

				// Send notification for reactivation
				$user->notify(new TelegramRegistrationNotification($user));

				// Fix IdTag handling
				$idTag = IdTag::where('entity_id', $user->id)
							->where('type', 'Customer')
							->first();
							
				if ($idTag) {
					$idTag->update([
						'status' => 'ACTIVE'
					]);
				} else {
					IdTag::create([
						'entity_id' => $user->id,
						'type' => 'Customer',
						'status' => 'ACTIVE'
					]);
				}

				// Handle login tokens
				$accessToken = null;
				$refreshToken = null;
				
				if ($findExistingCustomer && $defaultVehicle && $user) {
					$dataAfterLogin = $this->registerLogin($user->username, $request->password);
					$accessToken = isset($dataAfterLogin['access_token']) ? $dataAfterLogin['access_token'] : null;
					$refreshToken = isset($dataAfterLogin['refresh_token']) ? $dataAfterLogin['refresh_token'] : null;
				}

				$findExistingCustomer['access_token'] = $accessToken;
				$findExistingCustomer['refresh_token'] = $refreshToken;

				DB::commit();
				
				return response()->json([
					'success' => true,
					'data' => $findExistingCustomer,
					'message' => 'Account reactivated successfully.',
					'has_received_bonus' => $findExistingCustomer->has_received_bonus
				]);
			}

			// Create a new customer if no existing one is found
			$customer = Customer::create([
				'name' => $request->name,
				'email' => $request->email ? $request->email : null,
				'country_code' => $request->country_code,
				'phone' => $phone,
				'status' => 'ACTIVE',
				'created_by' => null,
				'updated_by' => null,
				'update_num' => 0,
				'points' => 0.00,
				'balance' => 0,
				'first_login_at' => Carbon::now(),
			]);

			CustomerHelper::updateCustomerMembership($customer->id);
			$bonusMessage = CustomerHelper::getBonusBalance($customer);

			$defaultVehicle = Helper::defaultVehicle();
			$customerVehicle = CustomerVehicle::create([
				'brand_id' => $request->brand_id ? $request->brand_id : $defaultVehicle['brand_id'],
				'model_id' => $request->model_id ? $request->model_id : $defaultVehicle['model_id'],
				'color_id' => $request->color_id ? $request->color_id : $defaultVehicle['color_id'],
				'year_id' => $request->year_id ? $request->year_id : $defaultVehicle['year_id'],
				'plate' => $request->plate,
				'is_default' => true,
				'customer_id' => $customer->id,
				'status' => 'ACTIVE',
			]);

			$user = User::create([
				'name' => $phone,
				'email' => $request->email ? $request->email : null,
				'image' => null,
				'username' => $phone,
				'password' => Hash::make($request->password),
				'customer_id' => $customer->id,
				'status' => 'ACTIVE',
				'created_by' => null,
				'updated_by' => null,
				'update_num' => 0
			]);

			IdTag::create([
				'entity_id' => $user->id,
				'type' => 'Customer',
				'status' => 'ACTIVE'
			]);

			$accessToken = null;
			$refreshToken = null;
			
			if ($customer && $customerVehicle && $user) {
				$dataAfterLogin = $this->registerLogin($user->username, $request->password);
				$accessToken = isset($dataAfterLogin['access_token']) ? $dataAfterLogin['access_token'] : null;
				$refreshToken = isset($dataAfterLogin['refresh_token']) ? $dataAfterLogin['refresh_token'] : null;
			}
			
			$customer['access_token'] = $accessToken;
			$customer['refresh_token'] = $refreshToken;
			
			$user->notify(new TelegramRegistrationNotification($user));

			DB::commit();

			return response()->json([
				'success' => true,
				'data' => $customer,
				'message' => 'Customer registered successfully.',
				'bonus_message' => $bonusMessage,
				'has_received_bonus' => $customer->has_received_bonus
			]);
			
		} catch (Exception $e) {
			DB::rollBack();
			return response()->json([
				'success' => false,
				'message' => $e->getMessage(),
			]);
		}
	}

	public function registerLogin($username, $password)
    {
        $user = User::where('username', $username)
            ->active()
            ->first();

        if (!$user || !Hash::check($password, $user->password)) {
            return [
                'access_token' => null,
                'refresh_token' => null,
            ];
        }

        $data = $this->getTokenAndRefreshToken($user);

        return [
            'access_token' => $data['access_token'] ?? null,
            'refresh_token' => $data['refresh_token'] ?? null,
        ];
    }

	 public function getTokenAndRefreshToken($user)
    {
        $request = request();

        $requestIdentity = 'User: ' . $user->username .
            ', Device: ' . $request->header('User-Agent') .
            ', IP: ' . $request->ip();

        $expireSeconds = (int) env('SANCTUM_TOKEN_EXPIRATION', 7200);
        $refreshTokenExpiration = (int) env('SANCTUM_REFRESH_EXPIRATION', 604800); // Default 7 days

        // Generate refresh token
        $refreshToken = Str::random(64);

        $refreshTokenId = Str::uuid();

        // Store the refresh token in the database with expiration date and revoked flag
        DB::table('refresh_tokens')->insert([
            'id' => $refreshTokenId,
            'user_id' => $user->id,
            'name' => $requestIdentity,
            'token' => hash('sha256', $refreshToken), // Securely store the hashed token
            'expires_at' => Carbon::now()->addSeconds($refreshTokenExpiration),
            'revoked' => false,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // Create main access token
        $token = $user->createToken($refreshTokenId, ['customer'], now()->addSeconds($expireSeconds));

        return [
            'access_token' => $token->plainTextToken,
            'token_type' => 'Bearer',
            'expires_in' => $expireSeconds,
            'refresh_token' => $refreshToken,
        ];
    }

	public function verifyPhoneNumber(VerifyPhoneNumberRequest $request) 
    {
        $phone = ltrim($request->phone, '0');
        $countryCode = $request->country_code;
        $len = $request->len ? (int) $request->len : 6;

        $isCustomerRegistered = Customer::where('phone', $phone)
                                        ->where('country_code', $countryCode)
                                        ->active()
                                        ->first();

        if($isCustomerRegistered) {
            return response()->json([
                'success' => false,
                'message' => 'Phone number already registered.',
            ]);
        } else {
            $sendOtp = OTPHelper::send($phone, $len);
            if($sendOtp['success']) {
                $userRegisteredOTP = UserRegisterOTP::create([
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
                'transaction_code' => $userRegisteredOTP->transaction_code,
                'expired_at' => $userRegisteredOTP->expired_at
            ]);
        }
		// return response()->json([
		// 	'success' => true,
		// 	'message' => 'OTP sent to your mobile number. Please verify.',
		// 	'transaction_code' => '123456',
		// 	'expired_at' => Carbon::now()->addMinute()
		// ]);
    }
	public function verifyOTP(VerifyOTPRequest $request) 
    {
        $otp = $request->otp;
        $transactionCode = $request->transaction_code;

        $verifyOTP = OTPHelper::verify(md5($otp. $transactionCode), $transactionCode);
        if ($verifyOTP && isset($verifyOTP['success']) && $verifyOTP['success'] === true) {
            $userRegisteredOTP = UserRegisterOTP::where('transaction_code', $transactionCode)->first();
            if($userRegisteredOTP) {
                $userRegisteredOTP->update([
                    'attempts' => $userRegisteredOTP->attempts + 1,
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

		// return response()->json([
        //     'success' => true,
        //     'message' => 'OTP verified successfully.',
        //     'transaction_code' => '123456'
        // ]);
    }
	public function resendOTP(ResendOTPRequest $request) 
    {
        $transactionCode = $request->transaction_code;
        $resendOTP = OTPHelper::resend($transactionCode);

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

		// return response()->json([
		// 	'success' => true,
		// 	'message' => 'OTP resend successfully.',
		// 	'transaction_code' => '123456'
		// ]);
    }
}

