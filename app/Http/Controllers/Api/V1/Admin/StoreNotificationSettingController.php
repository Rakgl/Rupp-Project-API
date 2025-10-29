<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendEmailTestNotification;
use App\Jobs\SendTelegramTestNotification;
use App\Models\Store;
use App\Models\StoreNotificationSetting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class StoreNotificationSettingController extends Controller
{
    /**
     * Display a listing of the resource for a specific store.
     */
    public function index(Store $store): JsonResponse
    {
        try {
            $settings = $store->notificationSettings()->get();
            return response()->json(['success' => true, 'data' => $settings]);
        } catch (\Exception $e) {
            Log::error("Error fetching notification settings for store {$store->id}: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'An error occurred.'], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Store $store): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'provider' => 'required|string|in:telegram,email,facebook',
            'name' => 'required|string|max:255',
            'credentials.bot_token' => 'required_if:provider,telegram|string',
            'credentials.chat_id' => 'required_if:provider,telegram|string',
            'credentials.thread_id' => 'nullable|string',
            'credentials.email' => 'required_if:provider,email|email',
            'credentials.smtp_host' => 'required_if:provider,email|string',
            'credentials.smtp_port' => 'required_if:provider,email|integer|min:1|max:65535',
            'credentials.smtp_username' => 'required_if:provider,email|string',
            'credentials.smtp_password' => 'required_if:provider,email|string',
            'credentials.smtp_encryption' => 'required_if:provider,email|string|in:none,tls,ssl',
            'credentials.from_email' => 'required_if:provider,email|email',
            'credentials.from_name' => 'nullable|string',
            'credentials.page_access_token' => 'required_if:provider,facebook|string',
            'credentials.recipient_id' => 'required_if:provider,facebook|string',
            'credentials.app_id' => 'nullable|string',
            'credentials.app_secret' => 'nullable|string',
            'is_active' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation failed.', 'errors' => $validator->errors()], 422);
        }

        try {
            $setting = $store->notificationSettings()->create($validator->validated());
            return response()->json(['success' => true, 'message' => 'Notification setting created successfully.', 'data' => $setting], 201);
        } catch (\Exception $e) {
            Log::error("Error creating notification setting: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'An error occurred during creation.'], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StoreNotificationSetting $notificationSetting): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'credentials.bot_token' => 'sometimes|required_if:provider,telegram|string',
            'credentials.chat_id' => 'sometimes|required_if:provider,telegram|string',
            'credentials.thread_id' => 'nullable|string',
            'credentials.email' => 'sometimes|required_if:provider,email|email',
            'credentials.smtp_host' => 'sometimes|required_if:provider,email|string',
            'credentials.smtp_port' => 'sometimes|required_if:provider,email|integer|min:1|max:65535',
            'credentials.smtp_username' => 'sometimes|required_if:provider,email|string',
            'credentials.smtp_password' => 'sometimes|required_if:provider,email|string',
            'credentials.smtp_encryption' => 'sometimes|required_if:provider,email|string|in:none,tls,ssl',
            'credentials.from_email' => 'sometimes|required_if:provider,email|email',
            'credentials.from_name' => 'nullable|string',
            'is_active' => 'sometimes|required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation failed.', 'errors' => $validator->errors()], 422);
        }

        try {
            $notificationSetting->update($validator->validated());
            return response()->json(['success' => true, 'message' => 'Notification setting updated successfully.', 'data' => $notificationSetting]);
        } catch (\Exception $e) {
            Log::error("Error updating notification setting {$notificationSetting->id}: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'An error occurred during update.'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StoreNotificationSetting $notificationSetting): JsonResponse
    {
        try {
            $notificationSetting->delete();
            return response()->json(['success' => true, 'message' => 'Notification setting deleted successfully.'], 200);
        } catch (\Exception $e) {
            Log::error("Error deleting notification setting {$notificationSetting->id}: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'An error occurred during deletion.'], 500);
        }
    }

    /**
     * Send a test notification for a given setting via queue.
     */
    public function test(StoreNotificationSetting $notificationSetting): JsonResponse
    {
        try {
            if ($notificationSetting->provider === 'telegram') {
                return $this->sendTelegramTest($notificationSetting);
            } elseif ($notificationSetting->provider === 'email') {
                return $this->sendEmailTest($notificationSetting);
            } elseif ($notificationSetting->provider === 'facebook') {
                return $this->sendFacebookTest($notificationSetting);
            }

            return response()->json(['success' => false, 'message' => 'Unsupported notification provider.'], 400);
        } catch (\Exception $e) {
            Log::error("Error dispatching test notification job for setting {$notificationSetting->id}: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to queue test notification.'], 500);
        }
    }

    /**
     * Queue a test Telegram notification with PDF document.
     */
    private function sendTelegramTest(StoreNotificationSetting $notificationSetting): JsonResponse
    {
        $credentials = $notificationSetting->credentials;
        $botToken = $credentials['bot_token'] ?? null;
        $chatId = $credentials['chat_id'] ?? null;

        if (!$botToken || !$chatId) {
            return response()->json(['success' => false, 'message' => 'Bot Token and Chat ID are required.'], 400);
        }

        try {
            // Dispatch the job to the queue
            SendTelegramTestNotification::dispatch($notificationSetting);

            Log::info("Telegram test notification job queued successfully", [
                'store_id' => $notificationSetting->store_id,
                'chat_id' => $chatId,
                'notification_setting_id' => $notificationSetting->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => "🚀 Test notification has been queued successfully! Your Telegram notification with PDF document will be sent shortly. Please check your chat in a few moments.",
                'debug_info' => [
                    'chat_id' => $chatId,
                    'thread_id' => $credentials['thread_id'] ?? null,
                    'queue_status' => 'dispatched',
                    'store_name' => $notificationSetting->store->name
                ]
            ]);

        } catch (\Exception $e) {
            Log::error("Error queuing test Telegram notification for setting {$notificationSetting->id}: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to queue test notification: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Queue a test email notification.
     */
    private function sendEmailTest(StoreNotificationSetting $notificationSetting): JsonResponse
    {
        $credentials = $notificationSetting->credentials;
        $requiredFields = ['email', 'smtp_host', 'smtp_port', 'smtp_username', 'smtp_password', 'smtp_encryption', 'from_email'];

        foreach ($requiredFields as $field) {
            if (empty($credentials[$field])) {
                return response()->json(['success' => false, 'message' => "Missing required field: {$field}"], 400);
            }
        }

        try {
            // Dispatch the job to the queue
            SendEmailTestNotification::dispatch($notificationSetting);

            Log::info("Email test notification job queued successfully", [
                'store_id' => $notificationSetting->store_id,
                'smtp_host' => $credentials['smtp_host'],
                'smtp_port' => $credentials['smtp_port'],
                'to_email' => $credentials['email'],
                'from_email' => $credentials['from_email'],
                'notification_setting_id' => $notificationSetting->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => "📧 Test email has been queued successfully! Your email notification with PDF letter will be sent shortly to {$credentials['email']}. Please check your inbox and spam folder in a few moments.",
                'debug_info' => [
                    'smtp_host' => $credentials['smtp_host'],
                    'smtp_port' => $credentials['smtp_port'],
                    'to_email' => $credentials['email'],
                    'from_email' => $credentials['from_email'],
                    'queue_status' => 'dispatched',
                    'store_name' => $notificationSetting->store->name
                ]
            ]);

        } catch (\Exception $e) {
            Log::error("Error queuing test email notification for setting {$notificationSetting->id}: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to queue test email: ' . $e->getMessage()], 500);
        }
    }


    /**
     * Send a test Facebook Messenger notification with PDF document.
     */
    private function sendFacebookTest(StoreNotificationSetting $notificationSetting): JsonResponse
    {
        $credentials = $notificationSetting->credentials;
        $pageAccessToken = $credentials['page_access_token'] ?? null;
        $recipientId = $credentials['recipient_id'] ?? null;

        if (!$pageAccessToken || !$recipientId) {
            return response()->json(['success' => false, 'message' => 'Page Access Token and Recipient ID are required.'], 400);
        }

        $storeName = $notificationSetting->store->name;

        try {
            // Generate PDF document
            $pdfPath = $this->generateTestNotificationPDF($notificationSetting);

            // Send text message first
            $message = "🏥 Test Notification for {$storeName}\n\n";
            $message .= "✅ Your Facebook Messenger notification settings are configured correctly!\n\n";
            $message .= "📋 Please find the detailed test report in the PDF document that will follow.\n\n";
            $message .= "🕐 Sent at: " . now()->format('Y-m-d H:i:s');

            $textPayload = [
                'recipient' => ['id' => $recipientId],
                'message' => ['text' => $message],
            ];

            // Send text message
            $textResponse = Http::withToken($pageAccessToken)
                ->post('https://graph.facebook.com/v18.0/me/messages', $textPayload);

            if (!$textResponse->successful()) {
                $errorDetails = $textResponse->json('error.message') ?? 'Unknown error occurred.';
                throw new \Exception("Failed to send text message: {$errorDetails}");
            }

            // Upload PDF as attachment first
            $uploadPayload = [
                'recipient' => json_encode(['id' => $recipientId]),
                'message' => json_encode([
                    'attachment' => [
                        'type' => 'file',
                        'payload' => [
                            'is_reusable' => false
                        ]
                    ]
                ]),
            ];

            // Send PDF document
            $documentResponse = Http::withToken($pageAccessToken)
                ->attach('filedata', file_get_contents($pdfPath), 'Test_Notification_Letter.pdf')
                ->post('https://graph.facebook.com/v18.0/me/messages', $uploadPayload);

            // Clean up PDF file
            if (file_exists($pdfPath)) {
                unlink($pdfPath);
            }

            if ($documentResponse->successful()) {
                Log::info("Facebook test notification with PDF sent successfully", [
                    'store_id' => $notificationSetting->store_id,
                    'recipient_id' => $recipientId,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => "📘 Test notification with PDF document sent successfully to Facebook Messenger! Check your messages for both the text and PDF attachment.",
                    'debug_info' => [
                        'recipient_id' => $recipientId,
                        'pdf_attached' => true,
                        'messages_sent' => 2,
                        'platform' => 'Facebook Messenger'
                    ]
                ]);
            } else {
                $errorDetails = $documentResponse->json('error.message') ?? 'Unknown error occurred.';
                throw new \Exception("Failed to send PDF document: {$errorDetails}");
            }

        } catch (\Exception $e) {
            // Clean up PDF file even on error
            if (isset($pdfPath) && file_exists($pdfPath)) {
                unlink($pdfPath);
            }

            Log::error("Error sending test Facebook notification for setting {$notificationSetting->id}: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to send test notification: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Generate a formal PDF notification letter
     */
    private function generateTestNotificationPDF(StoreNotificationSetting $notificationSetting): string
    {
        $store = $notificationSetting->store;
        $currentDate = now()->format('F j, Y');
        $currentTime = now()->format('g:i A');

        // Create HTML content for the PDF
        $html = view('emails.test-notification-letter', [
            'store' => $store,
            'notificationSetting' => $notificationSetting,
            'currentDate' => $currentDate,
            'currentTime' => $currentTime,
        ])->render();

        // Generate PDF
        $pdf = Pdf::loadHTML($html);
        $pdf->setPaper('A4', 'portrait');

        // Create temporary file
        $fileName = 'test_notification_' . $store->id . '_' . time() . '.pdf';
        $filePath = storage_path('app/temp/' . $fileName);

        // Ensure temp directory exists
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        // Save PDF to temporary file
        $pdf->save($filePath);

        return $filePath;
    }

    /**
     * Debug email configuration to help troubleshoot issues
     */
    public function debugEmailConfig(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'current_mail_config' => [
                'default_driver' => config('mail.default'),
                'smtp_host' => config('mail.mailers.smtp.host'),
                'smtp_port' => config('mail.mailers.smtp.port'),
                'smtp_username' => config('mail.mailers.smtp.username'),
                'smtp_encryption' => config('mail.mailers.smtp.encryption'),
                'from_address' => config('mail.from.address'),
                'from_name' => config('mail.from.name'),
            ],
            'environment' => app()->environment(),
            'queue_driver' => config('queue.default'),
            'log_channel' => config('logging.default'),
        ]);
    }

    public function getChatId(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'bot_token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'A valid Bot Token is required.'], 422);
        }

        $botToken = $request->input('bot_token');

        try {
            $response = Http::get("https://api.telegram.org/bot{$botToken}/getUpdates", [
                'offset' => -1,
                'limit' => 1,
            ]);

            if (!$response->successful() || !$response->json('ok')) {
                return response()->json(['success' => false, 'message' => 'Invalid Bot Token or failed to connect to Telegram.'], 400);
            }

            $updates = $response->json('result');
            if (empty($updates)) {
                return response()->json(['success' => false, 'message' => 'No recent messages found. Please send a message to the bot or add it to a group or channel first.'], 404);
            }

            $update = $updates[0];
            $chatId = null;

            // Check for a standard message, a chat member update, or a channel post
            if (isset($update['message']['chat']['id'])) {
                $chatId = $update['message']['chat']['id'];
            } elseif (isset($update['my_chat_member']['chat']['id'])) {
                $chatId = $update['my_chat_member']['chat']['id'];
            } elseif (isset($update['channel_post']['chat']['id'])) {
                $chatId = $update['channel_post']['chat']['id'];
            }

            if (!$chatId) {
                return response()->json(['success' => false, 'message' => 'Could not extract Chat ID from the latest bot activity.'], 404);
            }

            return response()->json(['success' => true, 'data' => ['chat_id' => $chatId]]);

        } catch (\Exception $e) {
            Log::error("Error fetching Telegram Chat ID: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'A server error occurred.'], 500);
        }
    }
}