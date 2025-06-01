<?php

namespace App\Helpers;

use App\Models\Notification;
use App\Jobs\SendPushNotificationJob;
use Illuminate\Support\Str;

class NotificationHelper
{
    /**
     * Create a notification for a transaction and dispatch it to the queue.
     *
     * @param string $customerId
     * @param string $title
     * @param string $message
     * @param string $type
     */
    public static function createAndDispatchTransactionNotification($customerId = null, $title, $message, $type = 'TRANSACTION')
    {
        $notification = Notification::create([
            'id' => Str::uuid(),
            'title' => json_encode(['en' => $title, 'kh' => $title]), // Update with language keys if needed
            'message' => json_encode(['en' => $message, 'kh' => $message]), // Update with language keys if needed
            'type' => $type,
            'status' => 'UNREAD',
            'customer_id' => $customerId,
        ]);

        SendPushNotificationJob::dispatch($notification);
    }
}
