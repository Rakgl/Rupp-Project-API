<?php

namespace App\Jobs;

use App\Models\StoreNotificationSetting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendEmailTestNotification implements ShouldQueue
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
        $requiredFields = ['email', 'smtp_host', 'smtp_port', 'smtp_username', 'smtp_password', 'smtp_encryption', 'from_email'];

        foreach ($requiredFields as $field) {
            if (empty($credentials[$field])) {
                Log::error("Missing email credential: {$field} for notification setting {$this->notificationSetting->id}");
                return;
            }
        }

        $storeName = $this->notificationSetting->store->name;
        $fromName = $credentials['from_name'] ?? $storeName;

        // Log the attempt for debugging
        Log::info("Processing email test notification via queue", [
            'store_id' => $this->notificationSetting->store_id,
            'smtp_host' => $credentials['smtp_host'],
            'smtp_port' => $credentials['smtp_port'],
            'smtp_encryption' => $credentials['smtp_encryption'],
            'from_email' => $credentials['from_email'],
            'to_email' => $credentials['email'],
            'job_id' => $this->job->getJobId(),
        ]);

        try {
            // Store original config to restore later
            $originalConfig = [
                'host' => config('mail.mailers.smtp.host'),
                'port' => config('mail.mailers.smtp.port'),
                'username' => config('mail.mailers.smtp.username'),
                'password' => config('mail.mailers.smtp.password'),
                'encryption' => config('mail.mailers.smtp.encryption'),
                'from_address' => config('mail.from.address'),
                'from_name' => config('mail.from.name'),
            ];

            // Set new configuration
            $newConfig = [
                'mail.mailers.smtp.host' => $credentials['smtp_host'],
                'mail.mailers.smtp.port' => (int)$credentials['smtp_port'],
                'mail.mailers.smtp.username' => $credentials['smtp_username'],
                'mail.mailers.smtp.password' => $credentials['smtp_password'],
                'mail.mailers.smtp.encryption' => $credentials['smtp_encryption'] === 'none' ? null : $credentials['smtp_encryption'],
                'mail.from.address' => $credentials['from_email'],
                'mail.from.name' => $fromName,
            ];

            config($newConfig);

            // Generate PDF letter
            $pdfPath = $this->generateTestNotificationPDF($this->notificationSetting);

            // Send the test email with PDF attachment
            $emailBody = "Dear {$storeName} Team,\n\n";
            $emailBody .= "Please find attached your test notification letter in PDF format.\n\n";
            $emailBody .= "This confirms that your email notification system is working correctly.\n\n";
            $emailBody .= "This email was sent via the notification queue system.\n\n";
            $emailBody .= "Best regards,\n";
            $emailBody .= "Repair Notification System\n\n";
            $emailBody .= "Sent at: " . now()->toDateTimeString();

            // Force synchronous sending (not queued)
            config(['mail.default' => 'smtp']);

            Mail::raw($emailBody, function ($message) use ($credentials, $fromName, $storeName, $pdfPath) {
                $message->to($credentials['email'])
                        ->subject("📄 Test Notification Letter from {$storeName}")
                        ->from($credentials['from_email'], $fromName)
                        ->attach($pdfPath, [
                            'as' => 'Test_Notification_Letter.pdf',
                            'mime' => 'application/pdf',
                        ]);
            });

            // Clean up temporary PDF file
            if (file_exists($pdfPath)) {
                unlink($pdfPath);
            }

            // Restore original configuration
            config([
                'mail.mailers.smtp.host' => $originalConfig['host'],
                'mail.mailers.smtp.port' => $originalConfig['port'],
                'mail.mailers.smtp.username' => $originalConfig['username'],
                'mail.mailers.smtp.password' => $originalConfig['password'],
                'mail.mailers.smtp.encryption' => $originalConfig['encryption'],
                'mail.from.address' => $originalConfig['from_address'],
                'mail.from.name' => $originalConfig['from_name'],
            ]);

            Log::info("Email test notification with PDF sent successfully via queue", [
                'to' => $credentials['email'],
                'from' => $credentials['from_email'],
                'store_id' => $this->notificationSetting->store_id,
                'job_id' => $this->job->getJobId(),
            ]);

        } catch (\Exception $e) {
            // Clean up PDF file even on error
            if (isset($pdfPath) && file_exists($pdfPath)) {
                unlink($pdfPath);
            }

            // Restore original configuration even on error
            if (isset($originalConfig)) {
                config([
                    'mail.mailers.smtp.host' => $originalConfig['host'],
                    'mail.mailers.smtp.port' => $originalConfig['port'],
                    'mail.mailers.smtp.username' => $originalConfig['username'],
                    'mail.mailers.smtp.password' => $originalConfig['password'],
                    'mail.mailers.smtp.encryption' => $originalConfig['encryption'],
                    'mail.from.address' => $originalConfig['from_address'],
                    'mail.from.name' => $originalConfig['from_name'],
                ]);
            }

            Log::error("Error sending test email notification via queue for setting {$this->notificationSetting->id}: " . $e->getMessage());
            throw $e; // Re-throw to trigger job retry
        }
    }

    public function failed(\Exception $exception)
    {
        Log::error("Email test notification job failed after {$this->tries} attempts", [
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