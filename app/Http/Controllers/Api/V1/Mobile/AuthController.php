<?php
namespace App\Http\Controllers\Api\V1\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

use App\Http\Requests\Api\V1\Mobile\MobileAuthenticationRequest;
use App\Models\User;
use App\Models\Customer;
use App\Models\CustomerVehicle;
use App\Models\IdTag;

use App\Helpers\CustomerHelper;
use App\Helpers\Helper;

class AuthController extends Controller
{
    public function login(MobileAuthenticationRequest $request)
    {
		$username = $request->username;
		$user = User::where('username', $username)
					->active()
					->first();

		if(!$user) { 
			return response()->json([
                'success' => false,
                'message' => 'User not found',
            ]);
		}

        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Incorrect username or password.',
            ]);
        }

        $data = $this->getTokenAndRefreshToken($user);


        return response()->json([
            'success' => true,
            'data'    => $data,
            'message' => 'Login successfully.'
        ]);
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
            'id'         => $refreshTokenId,
            'user_id'    => $user->id,
            'name'       => $requestIdentity,
            'token'      => hash('sha256', $refreshToken),
            'expires_at' => Carbon::now()->addSeconds($refreshTokenExpiration),
            'revoked'    => false,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // Create main access token
        $token = $user->createToken($refreshTokenId, ['customer'], now()->addSeconds($expireSeconds));

        return [
            'access_token'  => $token->plainTextToken,
            'token_type'    => 'Bearer',
            'expires_in'    => $expireSeconds,
            'refresh_token' => $refreshToken,
        ];
    }

    public function getRefreshTokenV1($refreshToken)
    {
        $clientId = env('MOBILE_CLIENT_ID');
        $clientSecret = env('MOBILE_CLIENT_SECRET');
        $response = Http::asForm()->post(env('PASSPORT_PORT') . '/oauth/token', [
            'grant_type'    => 'refresh_token',
            'refresh_token' => $refreshToken,
            'client_id'     => $clientId,
            'client_secret' => $clientSecret,
            'scope'         => '',
        ]);
        $result = json_decode((string) $response->getBody(), true);
        return $result;
    }

    public function getUserInfo()
    {
        $user = User::find(auth()->user()->id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.'
            ]);
        }
        $customer = Customer::where('id', $user->customer_id)
            ->active()
            ->first();

        return response()->json([
            'success' => true,
            'data'    => new \App\Http\Resources\Api\V1\Mobile\UserInfoResource($customer),
        ]);
    }

    public function getUserVehicleInfo()
    {
        $user = User::find(auth()->user()->id);
        $customer = Customer::where('id', $user->customer_id)
            ->active()
            ->first();

        $customerVehicle = CustomerVehicle::where('customer_id', $customer->id)
            ->where('status', 'ACTIVE')
            ->where('is_default', true)
            ->first();
        if (!$customerVehicle) {
            return response()->json([
                'success' => true,
                'message' => 'Vehicle not found',
                'data'    => null
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Vehicle info',
            'data'    => new \App\Http\Resources\Api\V1\Mobile\UserVehicleInfoResource($customerVehicle),
        ]);
    }

    public function logout()
    {
        try {
            $user = auth()->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not authenticated.',
                ], 401);
            }

            User::where('id', $user->id)->update([
                'fcm_token' => null
            ]);

            $currentToken = auth()->user()->currentAccessToken();
            if (!$currentToken) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not authenticated.',
                ], 401);
            }

            DB::beginTransaction();
            try {
                DB::table('refresh_tokens')
                    ->where('id', $currentToken->name)
                    ->update([
                        'revoked'    => true,
                        'updated_at' => Carbon::now()
                    ]);

                DB::table('personal_access_tokens')
                    ->where('name', $currentToken->name)
                    ->delete();

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Logout successfully.',
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('Logout failed: ' . $e->getMessage(), [
                    'user_id' => $user->id,
                    'trace'   => $e->getTraceAsString()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Logout failed. Please try again.'
                ], 500);
            }

        } catch (\Exception $e) {
            \Log::error('Logout failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Logout failed. Please try again.'
            ], 500);
        }
    }

    public function refreshToken(Request $request)
    {
        if (!$request->refresh_token) {
            return response()->json([
                'success' => false,
                'message' => 'Refresh token is required.'
            ], 400);
        }

        $refreshTokenRecord = DB::table('refresh_tokens')
            ->where('token', hash('sha256', $request->refresh_token))
            ->where('revoked', false)
            ->first();

        if (!$refreshTokenRecord || Carbon::now()->greaterThan($refreshTokenRecord->expires_at)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired refresh token.'
            ], 401);
        }

        $user = User::where('id', $refreshTokenRecord->user_id)
            ->where('status', 'ACTIVE')
            ->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or inactive user.'
            ], 404);
        }

        try {
            DB::beginTransaction();

            DB::table('personal_access_tokens')
                ->where('name', $refreshTokenRecord->id)
                ->delete();

            $expireSeconds = (int) env('SANCTUM_TOKEN_EXPIRATION', 7200);

            $token = $user->createToken($refreshTokenRecord->id, ['customer'], now()->addSeconds($expireSeconds));

            DB::commit();

            return response()->json([
                'success' => true,
                'data'    => [
                    'access_token'  => $token->plainTextToken,
                    'token_type'    => 'Bearer',
                    'expires_in'    => $expireSeconds,
                    'refresh_token' => $request->refresh_token,
                ],
                'message' => 'Refresh token successfully.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Token refresh failed: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'trace'   => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Token refresh failed. Please try again.'
            ], 500);
        }
    }

    public function registerLogin($username, $password)
    {
        $user = User::where('username', $username)
            ->active()
            ->first();

        if (!$user || !Hash::check($password, $user->password)) {
            return [
                'access_token'  => null,
                'refresh_token' => null,
            ];
        }

        $data = $this->getTokenAndRefreshToken($user);

        return [
            'access_token'  => $data['access_token'] ?? null,
            'refresh_token' => $data['refresh_token'] ?? null,
        ];
    }
}
