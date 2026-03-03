<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppVersion;
use Illuminate\Http\Request;

class AppVersionController extends Controller
{
    /**
     * List all version configurations.
     */
    public function index()
    {
        $versions = AppVersion::orderBy('app')->orderBy('platform')->get();
        
        return response()->json([
            'status' => 'success',
            'data' => $versions
        ]);
    }

    /**
     * Store or Update a Version Configuration.
     * * Endpoint: POST /api/v1/admin/app-versions
     */
    public function updateConfig(Request $request)
    {
        $validated = $request->validate([
            'app' => 'required|string|in:PATIENT,DOCTOR,PHARMACY,KIOSK',
            'platform' => 'required|string|in:IOS,ANDROID',
            'latest_version' => 'required|string',
            'min_supported_version' => 'nullable|string',
            'update_url' => 'required|url',
            'force_update' => 'boolean',
            'title' => 'nullable|string',
            'message' => 'nullable|string',
        ]);

        // Create or update the specific row for this App + Platform combination
        $version = AppVersion::updateOrCreate(
            [
                'app' => $validated['app'], 
                'platform' => $validated['platform']
            ],
            $validated
        );

        return response()->json([
            'status' => 'success',
            'message' => 'App version configuration updated successfully.',
            'data' => $version
        ]);
    }
}