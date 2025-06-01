<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\Mobile\AnnouncementRequest;
use App\Http\Resources\Api\V1\Admin\AuditResource;
use App\Models\Announcement;
use Illuminate\Http\Request;
use App\Helpers\AppHelper;
use App\Helpers\Helper;
use App\Http\Resources\Api\V1\Admin\Announcement\AnnouncementEditResource;
use App\Http\Resources\Api\V1\Admin\Announcement\AnnouncementIndexResource;
use App\Http\Resources\Api\V1\Admin\Announcement\AnnouncementShowResource;
use App\Models\AppVersion;
use App\Models\Notification as ModelsNotification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class AnnouncementController extends Controller
{
	// protected $messaging;
	// public function __construct()
    // {
    //     $messaging = (new Factory)
    //         ->withServiceAccount(config('services.firebase.credentials'))
    //         ->createMessaging();

	// 	$this->messaging = $messaging;
    // }

	protected $messaging;

    public function __construct()
    {
        $this->messaging = app('firebase.messaging');
    }


	public function send(Request $request)
	{
		try {
			$announcement = Announcement::findOrFail($request->id);
			$successCount = 0;
			$failureCount = 0;
			$image = $announcement->image  ? Helper::imageUrl($announcement->image) : 'no url';

			$title = is_array($announcement->title) ? $announcement->title : json_decode($announcement->title, true);
			$message = is_array($announcement->message) ? $announcement->message : json_decode($announcement->message, true);

			$message = strip_tags($message['en']);

			User::whereNotNull('fcm_token')
				->chunk(500, function ($users) use ($announcement, $image, &$successCount, &$failureCount, &$title, &$message) {
					$tokens = $users->pluck('fcm_token')->toArray();

					if (empty($tokens)) {
						return true;
					}

					$message = CloudMessage::new()
								->withNotification(
									Notification::create($title['en'], $message)
										->withImageUrl($image)
								)
								->withData($announcement->toArray());

					// Send to this chunk of users
					$response = $this->messaging->sendMulticast($message, $tokens);

					$successCount += $response->successes()->count();
					$failureCount += $response->failures()->count();

					foreach ($response->failures() as $index => $failure) {
						try {
							$invalidToken = $tokens[$index];

							User::where('fcm_token', $invalidToken)->update(['fcm_token' => null]);

							Log::warning('FCM Notification failed', [
								'token' => $invalidToken,
								'error' => $failure->error()->getMessage(),
								'index' => $index
							]);
						} catch (\Exception $e) {
							Log::error('Error handling FCM failure', [
								'error' => $e->getMessage(),
								'index' => $index
							]);
						}
					}
				});
			if ($successCount === 0 && $failureCount === 0) {
				return response()->json([
					'success' => true,
					'message' => 'No users with FCM tokens found'
				]);
			}

			$type = $announcement->type == 'PROMOTION' ? 'PROMOTION' : 'ALERT';
			ModelsNotification::create([
				'title' => $announcement->title,
				'message' => $announcement->message,
				'type' => $type,
				'status' => 'ACTIVE',
				'image' => $announcement->image,
				'is_read' => 0,
				'created_at' => now(),
				'updated_at' => now()
			]);

			$announcement->update([
				'status' => 'SENT',
				'sent_at' => Carbon::now(),
				'sent_by' => auth()->user()->id
			]);

			return response()->json([
				'success' => true,
				'status' => 'success',
				'message' => 'Announcement sent successfully',
				'details' => [
					'success_count' => $successCount,
					'failure_count' => $failureCount
				]
			]);

		} catch (\Exception $e) {
			Log::error('Failed to send announcement: ' . $e->getMessage());

			return response()->json([
				'status' => 'error',
				'message' => 'Failed to send announcement',
				'error' => $e->getMessage()
			], 500);
		}
	}
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
		$data = Announcement::where(function ($q) use ($request) {
			if($request->search) {
				$q->where('title', 'like', '%' . $request->search . '%')
					->orWhere('message', 'like', '%' . $request->search . '%');
			}

            if ($request->title) {
                $q->where('title', 'like', '%' . $request->title . '%');
            }

            if ($request->status) {
                $q->where('status', $request->status);
            }
		})->notDelete()->orderBy('created_at', 'desc')
			->paginate($request->per_page);
		$resource = AnnouncementIndexResource::collection($data)->response()->getData(true);
		return response()->json([
			'data' => $resource['data'],
			'meta' => [
				'current_page' => $resource['meta']['current_page'],
				'last_page' => $resource['meta']['last_page'],
				'total' => $resource['meta']['total'],
			]
		]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AnnouncementRequest $request)
    {
        try {
			DB::beginTransaction();

			$path = 'uploads/images';
			$imagePath = $request->hasFile('image') ? AppHelper::uploadImage($request->file('image'), $path) : null;

			Log::info('image path' . $imagePath);
			$announcement = Announcement::create([
				'title' => $request->title,
				'message' => $request->message,
				'type' => $request->type,
				'image' => $imagePath,
				'status' => 'PENDING',
				'created_at' => Carbon::now(),
				'created_by' => auth()->user()->id,
				'updated_at' => Carbon::now(),
			]);

			if($request->type == 'APP_VERSION') {
				if($request->platform == 'ALL') {
					$platforms = ['ANDROID', 'IOS'];
					foreach	($platforms as $platform) {
						$forceUpdate = $request->force_update == 'YES' ? true : false;
						$url = $platform == 'IOS' ? $request->ios_url : $request->android_url;
						AppVersion::create([
							'announcement_id' => $announcement->id,
							'platform' => $platform,
							'latest_version' => $request->version,
							'force_update' => $forceUpdate,
							'update_url' => $url,
							'message' => $request->message
						]);
					}
				} else {
					$forceUpdate = $request->force_update == 'YES' ? true : false;
					$url = $request->platform == 'IOS' ? $request->ios_url : $request->android_url;
					AppVersion::create([
						'announcement_id' => $announcement->id,
						'platform' => $request->platform,
						'latest_version' => $request->version,
						'force_update' => $forceUpdate,
						'update_url' => $request->update_url,
						'message' => $request->message
					]);
				}
			}
			DB::commit();
			return response()->json([
				'success' => true,
				'message' => 'Announcement created successfully',
			], 201);
		} catch (\Exception $e) {
			DB::rollBack();
			return response()->json([
				'success' => false,
				'message' => 'Failed to create announcement',
				'error' => $e->getMessage()
			]);
		}
    }

	public function edit(string $id)
	{
		$data = Announcement::find($id);

        if(!$data){
            return response()->json([
                'success' => false,
                'message' => 'Announcement not found'
            ],404);
        }
        return new AnnouncementEditResource($data);
	}
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = Announcement::find($id);

        if(!$data){
            return response()->json([
                'success' => false,
                'message' => 'Announcement not found'
            ],404);
        }
        return new AnnouncementShowResource($data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AnnouncementRequest $request, string $id)
    {
        $data = Announcement::find($id);

        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'Record not found',
            ], 404);
        }

		$imagePath = Helper::updateImage($request, $data);

		$data->update([
			'title' => $request->title,
			'message' => $request->message,
			'type' => $request->type,
			'scheduled_at' => $request->scheduled_at,
			'status' => 'PENDING',
			'image' => $imagePath,
			'update_num' => $data->update_num + 1,
			'updated_at' => Carbon::now(),
			'updated_by' => auth()->user()->id,
		]);

		AppVersion::where('announcement_id', $data->id)->delete();
		if($request->type == 'APP_VERSION') {
				if($request->platform == 'ALL') {
					$platforms = ['ANDROID', 'IOS'];
					foreach	($platforms as $platform) {
						$forceUpdate = $request->force_update == 'YES' ? true : false;
						$url = $platform == 'IOS' ? $request->ios_url : $request->android_url;
						AppVersion::create([
							'announcement_id' => $data->id,
							'platform' => $platform,
							'latest_version' => $request->version,
							'force_update' => $forceUpdate,
							'update_url' => $url,
							'message' => $request->message
						]);
					}
				} else {
					$forceUpdate = $request->force_update == 'YES' ? true : false;
					$url = $request->platform == 'IOS' ? $request->ios_url : $request->android_url;
					AppVersion::create([
						'announcement_id' => $data->id,
						'platform' => $request->platform,
						'latest_version' => $request->version,
						'force_update' => $forceUpdate,
						'update_url' => $request->update_url,
						'message' => $request->message
					]);
				}
			}

        return response()->json([
            'success' => true,
            'message' => 'Record has been updated successfully',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $data = Announcement::find($id);

        if(!$data){
            return response()->json([
                'success' => false,
                'message' => 'Announcement not found'
            ],404);
        }

        $data->status = 'DELETED';
        $data->save();

        return  response()->json([
            'success' => true,
            'message' => 'Announcement deleted successfully',
        ],200);
    }
    public function audit($id)
	{
		$announcement = Announcement::find($id);

		if (!$announcement) {
			return response()->json([
				'success' => false,
				'message' => "Announcement not found."
			]);
		}
		$audits = $announcement->audits()->with('user')->latest()->paginate(10);

		return response()->json([
			'success' => true,
			'items' => AuditResource::collection($audits)
		]);
	}
}
