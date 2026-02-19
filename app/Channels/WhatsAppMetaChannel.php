<?php

namespace App\Channels;

use App\Services\WhatsAppService;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class WhatsAppMetaChannel
{
    public function __construct(
        protected WhatsAppService $whatsAppService
    ) {}

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
            $phoneNumber = $this->whatsAppService->getPhoneNumber($notifiable);

            if (! $phoneNumber) {
                Log::warning('WhatsApp Meta notification skipped: No phone number for user', [
                    'user_id' => $notifiable->id ?? null,
                    'notification' => get_class($notification),
                ]);

                return;
            }

            // Handle different message types
            if (is_array($message)) {
                $this->sendComplexMessage($phoneNumber, $message, $notifiable);
            } else {
                $this->whatsAppService->sendMessage($phoneNumber, $message);
            }

            Log::info('WhatsApp Meta notification sent successfully', [
                'user_id' => $notifiable->id ?? null,
                'phone' => $phoneNumber,
                'notification' => get_class($notification),
            ]);

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
     * Send complex message (with media or documents)
     */
    protected function sendComplexMessage(string $phoneNumber, array $message, $notifiable): void
    {
        $messageBody = $message['body'] ?? '';

        // If there's a media URL, send as media
        if (! empty($message['media_url'])) {
            $mediaType = $this->whatsAppService->detectMediaType($message['media_url']);
            $this->whatsAppService->sendMedia($phoneNumber, $message['media_url'], $mediaType, $messageBody);
        }
        // If there are documents, send them
        elseif (! empty($message['documents']) && is_array($message['documents'])) {
            foreach ($message['documents'] as $document) {
                $this->whatsAppService->sendDocument(
                    $phoneNumber,
                    $document['url'],
                    $document['filename'] ?? null,
                    $document['caption'] ?? null
                );
            }

            // Send text message after documents if there's a body
            if ($messageBody) {
                $this->whatsAppService->sendMessage($phoneNumber, $messageBody);
            }
        }
        // Otherwise just send text
        else {
            $this->whatsAppService->sendMessage($phoneNumber, $messageBody);
        }
    }
}
