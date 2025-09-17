<?php

namespace App\Http\Controllers\Api\V1\Admin\Configuration;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Helpers\AppHelper;
use App\Helpers\Helper;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function show()
    {
        $status = [];
        $generalSettingsArray = [];
        $settings = Setting::all();

        foreach ($settings as $setting) {
            if ($setting->setting_key == 'app_logo' && !empty($setting->setting_value)) {
                $generalSettingsArray[$setting->setting_key] = Helper::imageUrl($setting->setting_value);
            } else {
                $generalSettingsArray[$setting->setting_key] = $setting->setting_value;
            }
        }

        $status["success"] = true;
        $status["data"] = $generalSettingsArray;

        return response()->json($status);
    }

    public function update(Request $request)
    {
        $status = [];
        Log::info('Setting update request received. All data:', $request->all());
        if ($request->exists('app_logo')) {
            Log::info('Value of app_logo input:', [$request->input('app_logo')]);
            Log::info('Is app_logo a file?', [$request->hasFile('app_logo')]);
        }

        $existingSettings = Setting::all()->keyBy('setting_key');

        $settingsDataForResponse = $existingSettings->map(function ($setting) {
            return $setting->setting_value;
        })->toArray();

        $anySettingActuallySaved = false;

        foreach ($request->all() as $requestKey => $requestValue) {
            if ($existingSettings->has($requestKey)) {
                $settingModel = $existingSettings->get($requestKey);

                $currentDbValue = $settingModel->getOriginal('setting_value');

                if ($requestKey == 'app_logo') {
                    $oldImagePath = $currentDbValue;

                    if ($request->hasFile('app_logo')) {
                        Log::info("Processing 'app_logo': New file detected for upload.");
                        if ($oldImagePath) {
                            if (Storage::exists($oldImagePath)) {
                                Storage::delete($oldImagePath);
                                Log::info("Processing 'app_logo': Deleted old physical image '{$oldImagePath}'.");
                            } else {
                                Log::warning("Processing 'app_logo': Old image '{$oldImagePath}' not found on default disk for deletion.");
                            }
                        }
                        $path = 'uploads/images';
                        $imagePath = AppHelper::uploadImage($request->file('app_logo'), $path);
                        $settingModel->setting_value = $imagePath;
                        Log::info("Processing 'app_logo': New image uploaded. DB path set to '{$imagePath}'.");

                    } elseif ($request->exists('app_logo') && $requestValue === '') {
                        Log::info("Processing 'app_logo': Received empty string, indicating removal of existing logo.");
                        if ($oldImagePath) {
                            if (Storage::exists($oldImagePath)) { // Check on default disk
                                Storage::delete($oldImagePath);   // Delete from default disk
                                Log::info("Processing 'app_logo': Deleted old physical image '{$oldImagePath}'.");
                            } else {
                                Log::warning("Processing 'app_logo': Old image '{$oldImagePath}' not found for deletion (when receiving empty string).");
                            }
                        }

                        if ($settingModel->setting_value !== null) {
                            $settingModel->setting_value = null;
                            Log::info("Processing 'app_logo': Database path set to null due to empty string request.");
                        } else {
                            Log::info("Processing 'app_logo': Database path was already null. No change for empty string request.");
                        }
                    }
                } else {
                    if (strval($currentDbValue) != strval($requestValue)) {
                        $settingModel->setting_value = $requestValue;
                        Log::info("Setting '{$requestKey}' marked for update from '{$currentDbValue}' to '{$requestValue}'.");
                    } else {
                        Log::info("Setting '{$requestKey}' already has the value '{$requestValue}'. No change needed.");
                    }
                }

                if ($settingModel->isDirty('setting_value')) {
                    $settingModel->save();
                    $settingsDataForResponse[$requestKey] = $settingModel->setting_value;
                    $anySettingActuallySaved = true;
                    Log::info("Setting '{$requestKey}' was dirty (new value: '{$settingModel->setting_value}', old: '{$settingModel->getOriginal('setting_value')}') and has been saved to database.");
                } else {
                    Log::info("Setting '{$requestKey}' was not dirty. No save needed. Current value: '{$currentDbValue}'");
                }

            } else {
                Log::info("Request key '{$requestKey}' not found in database settings. Skipping '{$requestKey}'.");
            }
        }

        if (isset($settingsDataForResponse['app_logo']) && !empty($settingsDataForResponse['app_logo'])) {
            $settingsDataForResponse['app_logo'] = Helper::imageUrl($settingsDataForResponse['app_logo']);
        } else {
            $settingsDataForResponse['app_logo'] = null;
        }

        $status["success"] = true;
        $status["message"] = $anySettingActuallySaved ? "General Settings have been updated." : "General Settings processed, no changes detected or applied.";
        $status["data"] = $settingsDataForResponse; // Return potentially updated settings
        return response()->json($status);
    }

    public function index(Request $request) // Added Request type hint for consistency
    {
        $status = []; // Initialize as an array
        $generalSettingsArray = [];
        $settings = Setting::all(); // Fetch all settings once

        foreach ($settings as $setting) {
            if ($setting->setting_key == 'app_logo' && !empty($setting->setting_value)) {
                $generalSettingsArray[$setting->setting_key] = Helper::imageUrl($setting->setting_value);
            } else {
                $generalSettingsArray[$setting->setting_key] = $setting->setting_value;
            }
        }
        $status["success"] = true;
        $status["data"] = $generalSettingsArray;
        return response()->json($status);
    }
}