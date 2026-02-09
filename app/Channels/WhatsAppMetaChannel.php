<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppMetaChannel
{
    protected string $apiVersion = 'v21.0';

    /**
     * Send the given notification.
     */
    public function send($notifiable, Notification $notification)
    {
        $phoneNumberId = config('services.meta.whatsapp_phone_number_id');
        $accessToken = config('services.meta.whatsapp_access_token');

        if (! $phoneNumberId || ! $accessToken) {
            Log::error('WhatsApp Meta notification failed: Credentials not configured', [
                'has_phone_id' => ! empty($phoneNumberId),
                'has_token' => ! empty($accessToken),
            ]);

            return;
        }

        $message = $notification->toWhatsApp($notifiable);

        if (! $message) {
            return;
        }

        try {
            $phoneNumber = $this->getPhoneNumber($notifiable);

            if (! $phoneNumber) {
                Log::warning('WhatsApp Meta notification skipped: No phone number for user', [
                    'user_id' => $notifiable->id ?? null,
                    'notification' => get_class($notification),
                ]);

                return;
            }

            // Remove + from phone number as Meta API expects it without
            $cleanPhone = ltrim($phoneNumber, '+');

            // Prepare message payload for Meta API
            $payload = $this->buildMessagePayload($cleanPhone, $message);

            // Send via Meta WhatsApp Cloud API
            $response = Http::timeout(30)
                ->withToken($accessToken)
                ->post("https://graph.facebook.com/{$this->apiVersion}/{$phoneNumberId}/messages", $payload);

            if ($response->successful()) {
                $data = $response->json();

                Log::info('WhatsApp Meta notification sent successfully', [
                    'user_id' => $notifiable->id ?? null,
                    'phone' => $phoneNumber,
                    'notification' => get_class($notification),
                    'message_id' => $data['messages'][0]['id'] ?? null,
                    'wamid' => $data['messages'][0]['message_status'] ?? null,
                ]);
            } else {
                Log::error('WhatsApp Meta notification failed: HTTP error', [
                    'user_id' => $notifiable->id ?? null,
                    'phone' => $phoneNumber,
                    'notification' => get_class($notification),
                    'status_code' => $response->status(),
                    'response_body' => $response->body(),
                    'error' => $response->json('error'),
                ]);
            }

        } catch (\Exception $e) {
            Log::error('WhatsApp Meta notification failed', [
                'user_id' => $notifiable->id ?? null,
                'error' => $e->getMessage(),
                'notification' => get_class($notification),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    /**
     * Build the message payload for Meta API
     */
    protected function buildMessagePayload(string $to, array|string $message): array
    {
        $messageBody = is_array($message) ? ($message['body'] ?? '') : $message;

        $payload = [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $to,
            'type' => 'text',
            'text' => [
                'preview_url' => false,
                'body' => $messageBody,
            ],
        ];

        // Handle media if present
        if (is_array($message) && ! empty($message['media_url'])) {
            $payload['type'] = $this->detectMediaType($message['media_url']);
            $payload[$payload['type']] = [
                'link' => $message['media_url'],
            ];

            // Add caption if it's not a document
            if ($payload['type'] !== 'document' && ! empty($messageBody)) {
                $payload[$payload['type']]['caption'] = $messageBody;
            }

            unset($payload['text']);
        }

        return $payload;
    }

    /**
     * Detect media type from URL
     */
    protected function detectMediaType(string $url): string
    {
        $extension = strtolower(pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION));

        return match ($extension) {
            'jpg', 'jpeg', 'png' => 'image',
            'mp4', 'avi', 'mov' => 'video',
            'mp3', 'ogg', 'amr' => 'audio',
            'pdf', 'doc', 'docx', 'xls', 'xlsx' => 'document',
            default => 'document',
        };
    }

    /**
     * Get the phone number from the notifiable model
     * Handles multiple phone number formats and normalizes them
     */
    protected function getPhoneNumber($notifiable): ?string
    {
        Log::info('WhatsApp Meta Channel parameters', [
            'app.env' => config('app.env'),
            'testing_mode' => config('services.meta.testing_mode'),
        ]);

        // If in testing mode, use specific testing phone
        if (config('app.env') === 'local' || config('services.meta.testing_mode')) {
            $testingPhone = config('services.meta.testing_phone');
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
