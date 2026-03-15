<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Service;
use App\Models\Store;
use App\Http\Resources\Api\V1\Mobile\Appointment\AppointmentResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the user's appointments.
     */
    public function index()
    {
        $appointments = Appointment::where('user_id', Auth::id())
            ->with('pet', 'service')
            ->latest()
            ->paginate(10);

        return AppointmentResource::collection($appointments)->additional([
            'success' => true,
            'message' => 'Appointments retrieved successfully.'
        ]);
    }

    /**
     * Store a newly created appointment in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'store_id' => 'nullable|uuid|exists:stores,id',
            'pet_id' => 'required|uuid|exists:pets,id,user_id,' . Auth::id(),
            'service_id' => 'required|uuid|exists:services,id',
            'start_time' => 'required|date|after:now',
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

        // Basic check for overlapping appointments for the same store or pet
        $overlapping = Appointment::where(function ($query) use ($storeId, $request) {
            $query->where('store_id', $storeId)
                  ->orWhere('pet_id', $request->pet_id);
        })
        ->where(function ($query) use ($startTime, $endTime) {
            $query->where('start_time', '<', $endTime)
                  ->where('end_time', '>', $startTime);
        })
        ->where('status', '!=', 'CANCELLED')
        ->exists();

        if ($overlapping) {
            throw ValidationException::withMessages([
                'start_time' => 'The selected time slot is no longer available.',
            ]);
        }

        $appointment = Appointment::create([
            'user_id' => Auth::id(),
            'store_id' => $storeId,
            'pet_id' => $request->pet_id,
            'service_id' => $request->service_id,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'special_requests' => $request->special_requests,
            'status' => 'PENDING',
        ]);

        $appointment->load('pet', 'service');

        return (new AppointmentResource($appointment))->additional([
            'success' => true,
            'message' => 'Appointment booked successfully.'
        ]);
    }

    /**
     * Display the specified appointment.
     */
    public function show(Appointment $appointment)
    {
        if ($appointment->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $appointment->load('pet', 'service');

        return new AppointmentResource($appointment);
    }

    /**
     * Cancel the specified appointment.
     */
    public function cancel(Appointment $appointment)
    {
        if ($appointment->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($appointment->status !== 'PENDING' && $appointment->status !== 'CONFIRMED') {
            return response()->json([
                'success' => false,
                'message' => 'This appointment cannot be cancelled.'
            ], 400);
        }

        $appointment->update(['status' => 'CANCELLED']);

        $appointment->load('pet', 'service');

        return (new AppointmentResource($appointment))->additional([
            'success' => true,
            'message' => 'Appointment cancelled successfully.'
        ]);
    }
}
