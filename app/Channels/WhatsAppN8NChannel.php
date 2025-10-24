<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppN8NChannel
{
    /**
     * Send the given notification.
     */
    public function send($notifiable, Notification $notification)
    {
        $message = $notification->toWhatsApp($notifiable);

        if (! $message) {
            return;
        }

        try {
            // Get the WhatsApp phone number from the notifiable model
            $phoneNumber = $this->getPhoneNumber($notifiable);

            if (! $phoneNumber) {
                Log::warning('WhatsApp N8N notification skipped: No phone number for user', [
                    'user_id' => $notifiable->id ?? null,
                    'notification' => get_class($notification),
                ]);

                return;
            }

            // Extract message body if it's an array
            $messageBody = is_array($message) ? ($message['body'] ?? '') : $message;

            $webHoorUrl = config('services.n8n.webhook_test_url');

            if(env('APP_ENV') === 'production') {
                $webHoorUrl = config('services.n8n.webhook_url');
            }

            // Make POST request to N8N webhook
            $response = Http::timeout(30)
                ->withHeaders([
                    'Token' => config('services.n8n.token'),
                ])
                ->post($webHoorUrl, [
                    'output' => $messageBody,
                    'userPhone' => $phoneNumber,
                ]);

            if ($response->successful()) {
                Log::info('WhatsApp N8N notification sent successfully', [
                    'user_id' => $notifiable->id ?? null,
                    'phone' => $phoneNumber,
                    'notification' => get_class($notification),
                    'response_status' => $response->status(),
                ]);
            } else {
                Log::error('WhatsApp N8N notification failed: HTTP error', [
                    'user_id' => $notifiable->id ?? null,
                    'phone' => $phoneNumber,
                    'notification' => get_class($notification),
                    'status_code' => $response->status(),
                    'response_body' => $response->body(),
                ]);
            }

        } catch (\Exception $e) {
            Log::error('WhatsApp N8N notification failed', [
                'user_id' => $notifiable->id ?? null,
                'error' => $e->getMessage(),
                'notification' => get_class($notification),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e; // Re-throw the original exception
        }
    }

    /**
     * Get the phone number from the notifiable model
     * Handles multiple phone number formats and normalizes them
     */
    protected function getPhoneNumber($notifiable): ?string
    {
        Log::info('WhatsApp N8N Channel parameters', [
            'app.env' => config('app.env'),
            'testing_mode' => config('services.n8n.testing_mode'),
        ]);

        // If in testing mode, use specific testing phone
        if (config('app.env') === 'local' || config('services.n8n.testing_mode')) {
            $testingPhone = config('services.n8n.testing_phone');
            if ($testingPhone) {
                return $this->normalizePhoneNumber($testingPhone);
            }
        }

        // Check for whatsapp_phone first, then fall back to phone
        $phone = $notifiable->whatsapp_phone ?? $notifiable->phone ?? null;

        if (! $phone || $phone === '0' || trim($phone) === '') {
            return null;
        }

        return $this->normalizePhoneNumber($phone);
    }

    /**
     * Normalize phone number to international format
     * Handles various formats like: +507 6694-8409, (507) 66266088, 50769121653, etc.
     */
    protected function normalizePhoneNumber(?string $phone): ?string
    {
        if (! $phone || $phone === '0' || trim($phone) === '') {
            return null;
        }

        // Remove all non-digit characters except + at the start
        $cleaned = preg_replace('/[^\d+]/', '', trim($phone));

        // If it's empty after cleaning, return null
        if (empty($cleaned) || $cleaned === '0') {
            return null;
        }

        // If already has + at the beginning, keep it
        if (str_starts_with($cleaned, '+')) {
            return $cleaned;
        }

        // If starts with 507 (Panama) but no +, add it
        if (str_starts_with($cleaned, '507') && strlen($cleaned) >= 11) {
            return '+'.$cleaned;
        }

        // If starts with 1 (USA/Canada) and has 11 digits, add +
        if (str_starts_with($cleaned, '1') && strlen($cleaned) === 11) {
            return '+'.$cleaned;
        }

        // If starts with 57 (Colombia) and has 12 digits, add +
        if (str_starts_with($cleaned, '57') && strlen($cleaned) >= 12) {
            return '+'.$cleaned;
        }

        // If starts with 593 (Ecuador) and has appropriate length, add +
        if (str_starts_with($cleaned, '593') && strlen($cleaned) >= 12) {
            return '+'.$cleaned;
        }

        // If starts with 506 (Costa Rica) and has 11 digits, add +
        if (str_starts_with($cleaned, '506') && strlen($cleaned) === 11) {
            return '+'.$cleaned;
        }

        // If it's 8 digits starting with 6, assume it's Panama without country code
        if (strlen($cleaned) === 8 && str_starts_with($cleaned, '6')) {
            return '+507'.$cleaned;
        }

        // If it's 7 digits, assume it's Panama without country code (landline format)
        if (strlen($cleaned) === 7) {
            return '+507'.$cleaned;
        }

        // Default: assume it's Panama if it's not already international format
        return '+507'.ltrim($cleaned, '0');
    }
}
