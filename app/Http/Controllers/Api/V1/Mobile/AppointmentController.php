<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Service;
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
            ->with('store', 'pet', 'service')
            ->latest()
            ->paginate(10);

        return response()->json($appointments);
    }

    /**
     * Store a newly created appointment in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'store_id' => 'required|uuid|exists:stores,id',
            'pet_id' => 'required|uuid|exists:pets,id,user_id,' . Auth::id(),
            'service_id' => 'required|uuid|exists:services,id',
            'start_time' => 'required|date|after:now',
            'special_requests' => 'nullable|string|max:1000',
        ]);

        $service = Service::findOrFail($request->service_id);
        $startTime = Carbon::parse($request->start_time);
        $endTime = $startTime->copy()->addMinutes($service->duration_minutes);

        // Basic check for overlapping appointments for the same store or pet
        $overlapping = Appointment::where(function ($query) use ($request) {
            $query->where('store_id', $request->store_id)
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
            'store_id' => $request->store_id,
            'pet_id' => $request->pet_id,
            'service_id' => $request->service_id,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'special_requests' => $request->special_requests,
            'status' => 'PENDING',
        ]);

        $appointment->load('store', 'pet', 'service');

        return response()->json($appointment, 201);
    }

    /**
     * Display the specified appointment.
     */
    public function show(Appointment $appointment)
    {
        if ($appointment->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $appointment->load('store', 'pet', 'service');

        return response()->json($appointment);
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
            return response()->json(['message' => 'This appointment cannot be cancelled.'], 400);
        }

        $appointment->update(['status' => 'CANCELLED']);

        $appointment->load('store', 'pet', 'service');

        return response()->json($appointment);
    }
}
