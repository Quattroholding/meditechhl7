<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client;

class WhatsAppChannel
{
    protected $client;

    public function __construct()
    {
        $sid = config('services.twilio.sid');
        $token = config('services.twilio.token');

        if ($sid && $token) {
            $this->client = new Client($sid, $token);
        }
    }

    /**
     * Send the given notification.
     */
    public function send($notifiable, Notification $notification)
    {
        if (! $this->client) {
            Log::error('WhatsApp notification failed: Twilio credentials not configured');

            return;
        }

        $message = $notification->toWhatsApp($notifiable);

        if (! $message) {
            return;
        }

        try {
            // Get the WhatsApp phone number from the notifiable model
            $phoneNumber = $this->getPhoneNumber($notifiable);

            if (! $phoneNumber) {
                Log::warning('WhatsApp notification skipped: No phone number for user', [
                    'user_id' => $notifiable->id ?? null,
                    'notification' => get_class($notification),
                ]);

                return;
            }

            $this->client->messages->create(
                "whatsapp:$phoneNumber", // To
                [
                    'from' => config('services.twilio.whatsapp_from'),
                    'body' => $message['body'] ?? $message,
                    'mediaUrl' => $message['media_url'] ?? null,
                ]
            );

            Log::info('WhatsApp notification sent successfully', [
                'user_id' => $notifiable->id ?? null,
                'phone' => $phoneNumber,
                'notification' => get_class($notification),
            ]);

        } catch (\Exception $e) {
            Log::error('WhatsApp notification failed', [
                'user_id' => $notifiable->id ?? null,
                'error' => $e->getMessage(),
                'notification' => get_class($notification),
            ]);
        }
    }

    /**
     * Get the phone number from the notifiable model
     */
    protected function getPhoneNumber($notifiable): ?string
    {
        // If in testing mode, use specific testing phone
        if (config('app.env') === 'local' || config('mail.testing_mode')) {
            $testingPhone = config('mail.testing_whatsapp_phone');
            if ($testingPhone) {
                return $testingPhone;
            }
        }

        // Check for whatsapp_phone first, then fall back to phone
        $phone = $notifiable->whatsapp_phone ?? $notifiable->phone ?? null;

        if (! $phone) {
            return null;
        }

        // Ensure phone number is in international format
        if (! str_starts_with($phone, '+')) {
            // Assume Colombian number if no country code
            $phone = '+507'.ltrim($phone, '0');
        }

        return $phone;
    }
}
