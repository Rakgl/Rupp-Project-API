<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\GeneralSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class GeneralSettingController extends Controller
{
    /**
     * Display a listing of the resource, grouped by the 'group' field.
     */
    public function index(): JsonResponse
    {
        try {
            $settings = GeneralSetting::orderBy('group')->orderBy('name')->get();
            $grouped = $settings->groupBy('group');

            return response()->json([
                'success' => true,
                'message' => 'General settings retrieved successfully.',
                'data' => $grouped,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching general settings: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'An error occurred while fetching settings.'], 500);
        }
    }

    /**
     * Update multiple general settings in a single request.
     */
    public function storeOrUpdate(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'settings' => 'required|array',
            'settings.*.id' => 'required|uuid|exists:general_settings,id',
            'settings.*.value' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation failed.', 'errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            foreach ($request->input('settings') as $settingData) {
                $setting = GeneralSetting::find($settingData['id']);

                if ($setting) {
                    $value = $settingData['value'];
                    if ($setting->type === 'boolean') {
                        $value = filter_var($value, FILTER_VALIDATE_BOOLEAN) ? '1' : '0';
                    }
                    $setting->value = $value;
                    $setting->save();
                }
            }
            DB::commit();
            return response()->json(['success' => true, 'message' => 'General settings updated successfully.'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating general settings: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'An error occurred during the update.'], 500);
        }
    }
}