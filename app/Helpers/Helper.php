<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\VehicleBrand;
use App\Models\VehicleColor;
use App\Models\VehicleModel;
use App\Models\VehicleYear;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class Helper
{
    public static function active() {
        return 'ACTIVE';
    }

    public static function inactive() {
        return 'INACTIVE';
    }

    public static function deleted() {
        return 'DELETED';
    }

    public static function getLoginUserId() {
        return Auth::user()->id;
    }

    public function isAdmin()
    {
        $admin = User::where('id', Auth::user()->id)->first();
        if ($admin->role == 'admin') {
            return true;
        } else {
            return false;
        }
    }
    public function isSuperAdmin()
    {
        $admin = User::where('id', Auth::user()->id)->first();
        if ($admin->role == 'super_admin') {
            return true;
        } else {
            return false;
        }
    }

    public static function generateOtp()
    {
        $otp = mt_rand(100000, 999999);
        return $otp;
    }

    public static function generateIdTag()
    {
        $prefix = sprintf('%04d', mt_rand(0, 9999));
        $sequenceValue = DB::selectOne("SELECT nextval('seq_id_tag') as value")->value;
        return $prefix . $sequenceValue;
    }

    public static function imageUrl($image)
    {
        return $image ? Storage::url($image) : null;
    }

    public static function defaultVehicle ()
    {
        $brand = VehicleBrand::active()->first();
        $model = VehicleModel::where('vehicle_brand_id', $brand->id)->active()->first();
        $color = VehicleColor::where('model_id', $model->id)->active()->first();
        $year = VehicleYear::where('color_id', $color->id)->active()->first();

        return [
            'brand_id' => $brand->id,
            'model_id' => $model->id,
            'color_id' => $color->id,
            'year_id' => $year->id
        ];
    }

    public static function formatJsonToArray(string $data) {
        $json = trim($data, '[]');
        $objects = explode('},', $json);
        $data = array_map(function($obj) {
            if (substr($obj, -1) !== '}') {
                $obj .= '}';
            }
            return $obj;
        }, $objects);

        return $data;
    }

    public static function formatDuration($seconds)
    {
        $hours = intdiv($seconds, 3600);
        $minutes = intdiv($seconds % 3600, 60);
        $remainingSeconds = $seconds % 60;

        if ($hours > 0) {
            return sprintf('%dh:%02dmn:%02ds', $hours, $minutes, $remainingSeconds);
        }

        return sprintf('%02dmn:%02ds', $minutes, $remainingSeconds);
    }

    public static function formatStatus($status)
    {
        $statuses = [
            // Generic statuses
            'ACTIVE'       => ['text' => 'ACTIVE',       'color' => 'primary', 'icon' => 'mdi-check-circle'],
            'INACTIVE'     => ['text' => 'INACTIVE',     'color' => 'warning', 'icon' => 'mdi-information'],
            'DELETED'      => ['text' => 'DELETED',      'color' => 'error',   'icon' => 'mdi-close-circle'],
            'PENDING'      => ['text' => 'PENDING',      'color' => 'warning', 'icon' => 'mdi-information'],
            'SUCCESS'      => ['text' => 'SUCCESS',      'color' => 'primary', 'icon' => 'mdi-check-circle'],
            'FAILED'       => ['text' => 'FAILED',       'color' => 'error',   'icon' => 'mdi-progress-close'],
            'SENT'         => ['text' => 'SENT',         'color' => 'primary', 'icon' => 'mdi-check-circle'],
            'LISTED'       => ['text' => 'LISTED',       'color' => 'info',    'icon' => 'mdi-map-marker'],
            'MAINTENANCE'  => ['text' => 'MAINTENANCE',  'color' => '',        'icon' => 'mdi-cogs'],
            'OPEN'         => ['text' => 'OPEN',         'color' => 'primary', 'icon' => 'mdi-check-circle'],
            'CLOSED'       => ['text' => 'CLOSED',       'color' => 'error',   'icon' => 'mdi-close-circle'],
            'RESOLVED'     => ['text' => 'RESOLVED',     'color' => 'primary', 'icon' => 'mdi-cogs'],
            'REJECTED'     => ['text' => 'REJECTED',     'color' => 'error',   'icon' => 'mdi-close-circle'],
            'APPROVED'     => ['text' => 'APPROVED',     'color' => 'primary', 'icon' => 'mdi-check-circle'],

            // OCPP statuses
            'Faulted'      => ['text' => 'Faulted',      'color' => 'error',   'icon' => 'mdi-alert-circle'],
            'SuspendedEV'  => ['text' => 'SuspendedEV',  'color' => 'warning', 'icon' => 'mdi-pause-circle'],
            'SuspendedEVSE'=> ['text' => 'SuspendedEVSE','color' => 'warning', 'icon' => 'mdi-pause-circle-outline'],
            'Starting'     => ['text' => 'Starting',     'color' => 'info',    'icon' => 'mdi-play-circle'],
            'Charging'     => ['text' => 'Charging',     'color' => 'info',    'icon' => 'mdi-battery-charging-outline'],
            'Finished'     => ['text' => 'Finished',     'color' => 'primary', 'icon' => 'mdi-check-circle'],
            'Finishing'    => ['text' => 'Finishing',    'color' => 'primary', 'icon' => 'mdi-check-circle-outline'],
            'Preparing'    => ['text' => 'Preparing',    'color' => 'warning', 'icon' => 'mdi-cogs'],
            'Available'    => ['text' => 'Available',    'color' => 'success', 'icon' => 'mdi-check-circle'],
            'Unavailable'  => ['text' => 'Unavailable',  'color' => 'grey lighten-2',   'icon' => 'mdi-power-plug-off'],
        ];

        return $statuses[$status] ?? ['text' => $status, 'color' => 'secondary', 'icon' => 'mdi-help-circle'];
    }

    public static function formatDate($date) {
        return $date ? date('d/m/y, h:i A', strtotime($date)) : null;
    }


    public static function formatDateRange($from, $to)
    {
        if (!$from && !$to) {
            return null;
        }
        $from = $from ? date('d/m/y', strtotime($from)) : '';
        $to = $to ? date('d/m/y', strtotime($to)) : '';


        $formattedFrom = $from ? $from : '';
        $formattedTo = $to ? $to : '';

        return $from === $to ? $formattedFrom : "{$formattedFrom}~{$formattedTo}";
    }

    public static function formatDateWithoutClock($date) {
        return $date ? date('d/m/y', strtotime($date)) : null;
    }

    public static function updateImage($request, $data)
    {
        $currentImagePath = $data->image;

        // Case 1: New file uploaded
        if ($request->hasFile('image')) {
            $path = 'uploads/images';

            // Delete old image if exists
            if ($currentImagePath && Storage::exists($currentImagePath)) {
                Storage::delete($currentImagePath);
            }

            return AppHelper::uploadImage($request->file('image'), $path);
        }

        // Case 2: Image removal (empty string or null)
        elseif ($request->exists('image')) {
            $imageValue = $request->input('image');

            // Check if image is being removed (empty string, null, or 'null' string)
            if ($imageValue === '' || $imageValue === null || $imageValue === 'null') {
                // Delete current image if exists
                if ($currentImagePath && Storage::exists($currentImagePath)) {
                    Storage::delete($currentImagePath);
                }
                return null;
            }

            // Case 3: Image value is a string (modern avatar JSON or existing URL)
            elseif (is_string($imageValue)) {
                // Check if it's a JSON string (modern avatar)
                if (self::isJsonString($imageValue)) {
                    // It's a modern avatar configuration, delete old file if exists
                    if ($currentImagePath && Storage::exists($currentImagePath)) {
                        Storage::delete($currentImagePath);
                    }
                    return $imageValue; // Store the JSON string
                }

                // Check if it's the same as current path (no change)
                if ($imageValue === $currentImagePath) {
                    return $currentImagePath;
                }

                // If it's a different URL/path, extract relative path and update it
                return self::extractRelativePath($imageValue);
            }
        }

        // Case 4: No image parameter provided, keep existing
        return $currentImagePath;
    }

    private static function extractRelativePath($imagePath)
    {
        // If it's already a relative path, return as is
        if (!filter_var($imagePath, FILTER_VALIDATE_URL)) {
            return $imagePath;
        }

        // Extract relative path from full URL
        $parsed = parse_url($imagePath);
        if (isset($parsed['path'])) {
            // Remove /storage/ prefix if present (Laravel storage URL)
            $path = $parsed['path'];
            if (strpos($path, '/storage/') === 0) {
                $path = substr($path, 9); // Remove '/storage/' prefix
            }
            return $path;
        }

        return $imagePath;
    }

    private static function isJsonString($string)
    {
        if (!is_string($string)) {
            return false;
        }

        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }

    public static function updateCover($request, $data)
    {
        $imagePath = $data->cover;
        if ($request->hasFile('cover')) {
            $path = 'uploads/images';
            $imagePath = AppHelper::uploadImage($request->file('cover'), $path);

            if ($data->cover && Storage::exists($data->cover)) {
                Storage::delete($data->cover);
            }
        } elseif (!$request->cover) {
            if ($data->cover && Storage::exists($data->cover)) {
                Storage::delete($data->cover);
            }
            $imagePath = null;
        }
        return $imagePath;
    }

    public static function updateMultipleImages($image) {
        if ($image instanceof UploadedFile) {
            $path = 'uploads/images';
            $imagePath = AppHelper::uploadImage($image, $path);
            return $imagePath;
        } else {
            return 'uploads/' . Str::after($image, 'uploads/');
        }
    }

    public static function updateVehicleImage($image, $data) {
        if (!$image) {
            if (isset($data) && $data->image && Storage::exists($data->image)) {
                Storage::delete($data->image);
            }
            $imagePath = null;
            return $imagePath;
        } else if($image instanceof UploadedFile) {
            $path = 'uploads/images';
            $imagePath = AppHelper::uploadImage($image, $path);
            if (isset($data) && $data->image && Storage::exists($data->image)) {
                Storage::delete($data->image);
            }
            return $imagePath;
        } else {
            if (isset($data) && $data->image && Storage::exists($data->image)) {
                Storage::delete($data->image);
            }
            return 'uploads/' . Str::after($image, 'uploads/');
        }
    }
}