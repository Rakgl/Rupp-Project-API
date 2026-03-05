<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    /**
     * Get the main dashboard summary statistics and recent lists.
     */
    public function index(): JsonResponse
    {
        try {
            $today = Carbon::today();
            $now = Carbon::now();
            
            $revenueToday = Order::whereDate('created_at', $today)
                ->where('status', 'COMPLETED')
                ->sum('total_amount');

            $pendingOrders = Order::where('status', 'PENDING')->count();
            $appointmentsToday = Appointment::whereDate('start_time', $today)->count();
            $newUsersToday = User::whereDate('created_at', $today)->count();

            $upcomingAppointments = Appointment::with(['pet', 'service', 'user'])
                ->where('start_time', '>=', $now)
                ->orderBy('start_time', 'asc')
                ->take(5)
                ->get();

            $recentOrders = Order::with(['user', 'paymentMethod'])
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Dashboard data retrieved successfully.',
                'data' => [
                    'kpis' => [
                        'revenue_today' => (float) $revenueToday,
                        'pending_orders' => $pendingOrders,
                        'appointments_today' => $appointmentsToday,
                        'new_users_today' => $newUsersToday,
                    ],
                    'lists' => [
                        'upcoming_appointments' => $upcomingAppointments,
                        'recent_orders' => $recentOrders,
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Dashboard Error: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'Failed to load dashboard data.'
            ], 500);
        }
    }
}