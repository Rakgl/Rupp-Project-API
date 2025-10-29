<?php

namespace App\Jobs;

use App\Models\StoreNotificationSetting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendTelegramTestNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $notificationSetting;
    public $tries = 3;
    public $timeout = 120;

    public function __construct(StoreNotificationSetting $notificationSetting)
    {
        $this->notificationSetting = $notificationSetting;
    }

    public function handle()
    {
        $credentials = $this->notificationSetting->credentials;
        $botToken = $credentials['bot_token'] ?? null;
        $chatId = $credentials['chat_id'] ?? null;
        $threadId = $credentials['thread_id'] ?? null;

        if (!$botToken || !$chatId) {
            Log::error("Missing Telegram credentials for notification setting {$this->notificationSetting->id}");
            return;
        }

        $storeName = $this->notificationSetting->store->name;
        
        try {
            // Generate PDF document
            $pdfPath = $this->generateTestNotificationPDF($this->notificationSetting);
            
            // Send text message first
            $message = "📄 *Test Notification for {$storeName}*\n\n";
            $message .= "✅ Your Telegram notification settings are configured correctly!\n\n";
            $message .= "📋 Please find the detailed test report in the PDF document attached below.\n\n";
            $message .= "🕐 Sent at: " . now()->format('Y-m-d H:i:s');

            $textPayload = [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'Markdown',
            ];

            if ($threadId) {
                $textPayload['message_thread_id'] = $threadId;
            }

            // Send text message
            $textResponse = Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", $textPayload);

            if (!$textResponse->successful() || !$textResponse->json('ok')) {
                $errorDetails = $textResponse->json('description') ?? 'Unknown error occurred.';
                throw new \Exception("Failed to send text message: {$errorDetails}");
            }

            // Send PDF document
            $documentPayload = [
                'chat_id' => $chatId,
                'caption' => "📄 *Test Notification Letter*\n\nOfficial test report for {$storeName} notification system.",
                'parse_mode' => 'Markdown',
            ];

            if ($threadId) {
                $documentPayload['message_thread_id'] = $threadId;
            }

            // Use multipart form data for file upload
            $documentResponse = Http::attach(
                'document', 
                file_get_contents($pdfPath), 
                'Test_Notification_Letter.pdf'
            )->post("https://api.telegram.org/bot{$botToken}/sendDocument", $documentPayload);

            // Clean up PDF file
            if (file_exists($pdfPath)) {
                unlink($pdfPath);
            }

            if ($documentResponse->successful() && $documentResponse->json('ok')) {
                Log::info("Telegram test notification with PDF sent successfully via queue", [
                    'store_id' => $this->notificationSetting->store_id,
                    'chat_id' => $chatId,
                    'thread_id' => $threadId,
                    'job_id' => $this->job->getJobId(),
                ]);
            } else {
                $errorDetails = $documentResponse->json('description') ?? 'Unknown error occurred.';
                throw new \Exception("Failed to send PDF document: {$errorDetails}");
            }

        } catch (\Exception $e) {
            // Clean up PDF file even on error
            if (isset($pdfPath) && file_exists($pdfPath)) {
                unlink($pdfPath);
            }

            Log::error("Error sending test Telegram notification via queue for setting {$this->notificationSetting->id}: " . $e->getMessage());
            throw $e; // Re-throw to trigger job retry
        }
    }

    public function failed(\Exception $exception)
    {
        Log::error("Telegram test notification job failed after {$this->tries} attempts", [
            'notification_setting_id' => $this->notificationSetting->id,
            'store_id' => $this->notificationSetting->store_id,
            'error' => $exception->getMessage(),
        ]);
    }

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
}