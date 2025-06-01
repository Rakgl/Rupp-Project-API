<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class AuthHelper
{
	public static function getTokenAndRefreshToken($username, $password) : array
    {
        $clientId = env('MOBILE_CLIENT_ID');
        $clientSecret = env('MOBILE_CLIENT_SECRET');
        $response = [];
        $response = Http::asForm()->post(env('PASSPORT_PORT') . '/oauth/token', [
            'grant_type' => 'password',
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'username' => $username,
            'password' => $password,
            'scope' => '',
        ]);
        $result = $response ?
            json_decode((string) $response->getBody(), true) :
            [
                'message' => 'Client not found.'
            ];

        return $result;
    }
	
	public function refreshToken(Request $request)
    {
        if(!$request->refreshToken){
            return response()->json([
                'success' => false,
                'message' => 'Refresh token is required.'
            ], 400);
        }

        $data = $this->getRefreshToken($request->refreshToken);
        if(isset($data['access_token']) && $data['access_token']){
            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $data['message']
        ], 401);
    }

	public static function getRefreshToken($refreshToken) 
	{
        $clientId = env('MOBILE_CLIENT_ID');
        $clientSecret = env('MOBILE_CLIENT_SECRET');
        $response = Http::asForm()->post(env('PASSPORT_PORT') . '/oauth/token', [
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken,
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'scope' => '',
        ]);
        $result = json_decode((string) $response->getBody(), true);
        return $result;
    }
}






