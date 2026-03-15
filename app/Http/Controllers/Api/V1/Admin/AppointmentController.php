<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Service;
use App\Models\User;
use App\Models\Store;
use App\Http\Resources\Api\V1\Admin\Appointment\AppointmentIndexResource;
use App\Http\Resources\Api\V1\Admin\Appointment\AppointmentShowResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $request->validate([
            'status' => 'nullable|string|in:PENDING,CONFIRMED,IN_CARE,COMPLETED,CANCELLED',
            'user_id' => 'nullable|uuid|exists:users,id',
            'store_id' => 'nullable|uuid|exists:stores,id',
        ]);

        $appointments = Appointment::query()
            ->when($request->status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->when($request->user_id, function ($query, $userId) {
                return $query->where('user_id', $userId);
            })
            ->when($request->store_id, function ($query, $storeId) {
                return $query->where('store_id', $storeId);
            })
            ->with('user', 'pet', 'service')
            ->latest()
            ->paginate($request->input('per_page', 10));

        return AppointmentIndexResource::collection($appointments)->additional([
            'success' => true,
            'message' => 'Appointments retrieved successfully.'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|uuid|exists:users,id',
            'store_id' => 'nullable|uuid|exists:stores,id',
            'pet_id' => ['required', 'uuid', 'exists:pets,id', Rule::exists('pets', 'id')->where('user_id', $request->user_id)],
            'service_id' => 'required|uuid|exists:services,id',
            'start_time' => 'required|date',
            'special_requests' => 'nullable|string|max:1000',
        ]);

        $storeId = $request->input('store_id', Store::first()?->id);

        if (!$storeId) {
            return response()->json([
                'success' => false,
                'message' => 'No store available for booking.'
            ], 422);
        }

        $service = Service::findOrFail($request->service_id);
        $startTime = Carbon::parse($request->start_time);
        $endTime = $startTime->copy()->addMinutes($service->duration_minutes);

        $appointment = Appointment::create([
            'user_id' => $request->user_id,
            'store_id' => $storeId,
            'pet_id' => $request->pet_id,
            'service_id' => $request->service_id,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'special_requests' => $request->special_requests,
            'status' => 'CONFIRMED', // Admin created appointments are confirmed by default
        ]);

        $appointment->load('user', 'pet', 'service');

        return (new AppointmentShowResource($appointment))->additional([
            'success' => true,
            'message' => 'Appointment created successfully.'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Appointment $appointment)
    {
        $appointment->load('user', 'pet', 'service');
        return new AppointmentShowResource($appointment);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Appointment $appointment)
    {
        $request->validate([
            'status' => 'required|string|in:PENDING,CONFIRMED,IN_CARE,COMPLETED,CANCELLED',
            'start_time' => 'sometimes|required|date',
            'special_requests' => 'nullable|string|max:1000',
        ]);

        $data = $request->only(['status', 'special_requests']);
        
        if ($request->has('start_time')) {
            $service = $appointment->service;
            $startTime = Carbon::parse($request->start_time);
            $endTime = $startTime->copy()->addMinutes($service->duration_minutes);
            $data['start_time'] = $startTime;
            $data['end_time'] = $endTime;
        }

        $appointment->update($data);

        $appointment->load('user', 'pet', 'service');

        return (new AppointmentShowResource($appointment))->additional([
            'success' => true,
            'message' => 'Appointment updated successfully.'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Appointment $appointment)
    {
        $appointment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Appointment deleted successfully.'
        ]);
    }
}
