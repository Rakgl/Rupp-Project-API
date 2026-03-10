<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\User;
// use App\Models\Wallet; 
use App\Http\Requests\Api\V1\Mobile\MobileAuthenticationRequest;

class AuthController extends Controller
{
    public function login(MobileAuthenticationRequest $request)
    {
        $phone = ltrim($request->phone, '0');
        $isNewRegistration = false;

        $user = User::where('phone', $phone)
            ->where('status', 'ACTIVE')
            ->first();

        if (!$user) {
            DB::beginTransaction();
            try {
                $user = User::create([
                    'id'         => (string) Str::uuid(),
                    'name'       => $request->name ?? 'User ' . $phone,
                    'email'      => $request->email ?? null,
                    'phone'      => $phone,
                    'username'   => $phone,
                    'password'   => Hash::make($request->password),
                    'status'     => 'ACTIVE',
                    'type'       => 'Mobile',
                    'update_num' => 0,
                ]);

                DB::commit();
                $isNewRegistration = true;
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Registration failed: ' . $e->getMessage(),
                ], 500);
            }
        }

        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Incorrect phone number or password.',
            ], 401);
        }

        $data = $this->getTokenAndRefreshToken($user);

        return response()->json([
            'success' => true,
            'data'    => $data,
            'message' => $isNewRegistration ? 'Registered and logged in successfully.' : 'Login successfully.'
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

        $refreshToken = Str::random(64);
        $refreshTokenId = (string) Str::uuid();

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
        $token = $user->createToken($refreshTokenId, ['mobile-user'], now()->addSeconds($expireSeconds));

        return [
            'access_token'  => $token->plainTextToken,
            'token_type'    => 'Bearer',
            'expires_in'    => $expireSeconds,
            'refresh_token' => $refreshToken,
        ];
    }

    public function getUserInfo()
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => [
                'id'      => $user->id,
                'name'    => $user->name,
                'phone'   => $user->phone,
                'email'   => $user->email,
                'address' => $user->address,
                'image'   => $user->image ? url('storage/' . $user->image) : null,
            ]
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

            $user->update(['fcm_token' => null]);

            $currentToken = $user->currentAccessToken();
            
            if ($currentToken) {
                DB::beginTransaction();
                try {
                    DB::table('refresh_tokens')
                        ->where('id', $currentToken->name)
                        ->update([
                            'revoked'    => true,
                            'updated_at' => Carbon::now()
                        ]);

                    $currentToken->delete();

                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    throw $e;
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Logout successfully.',
            ]);

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

            $token = $user->createToken($refreshTokenRecord->id, ['mobile-user'], now()->addSeconds($expireSeconds));

            DB::commit();

            return response()->json([
                'success' => true,
                'data'    => [
                    'access_token'  => $token->plainTextToken,
                    'token_type'    => 'Bearer',
                    'expires_in'    => $expireSeconds,
                    'refresh_token' => $request->refresh_token,
                ],
                'message' => 'Token refreshed successfully.'
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
}