<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\Mobile\NotificationResource;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
	public function index(Request $request)
	{
		$query = Notification::query();
		
		if ($request->type) {
			$query->where('type', $request->type);
		}
		
		if ($request->type === 'TRANSACTION') {
			$query->where('customer_id', auth()->user()->customer->id);
		}
		
		$query->latest();

		$data = $query->paginate($request->per_page);
		
		$resource = NotificationResource::collection($data)->response()->getData(true);
		
		return response()->json([
			'success' => true,
			'message' => 'Notifications retrieved successfully',
			'data' => $resource['data'],
			'meta' => [
				'current_page' => $resource['meta']['current_page'],
				'last_page' => $resource['meta']['last_page'],
				'total' => $resource['meta']['total'],
				'read' => Notification::where('is_read', false)->count(),
			]
		]);
	}

	public function markAsRead(Request $request)
	{
		Notification::where('is_read', false)
			->update([
				'is_read' => true,
				'read_at' => now()
			]);

		return response()->json([
			'success' => true,
			'message' => 'Notification marked as read successfully',
		]);
	}
}
