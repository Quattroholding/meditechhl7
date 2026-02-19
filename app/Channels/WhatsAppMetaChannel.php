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
        // If message should use template (bypasses 24-hour window restriction)
        if (! empty($message['use_template'])) {
            $this->sendTemplateMessage($phoneNumber, $message, $notifiable);

            return;
        }

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

    /**
     * Send template message (for business-initiated conversations)
     */
    protected function sendTemplateMessage(string $phoneNumber, array $message, $notifiable): void
    {
        $templateName = $message['template_name'] ?? 'prescription_delivery';
        $documents = $message['documents'] ?? [];

        // Build template components
        $components = [];

        // Body component with variables (patient name, doctor name)
        $bodyParameters = [];
        if (! empty($message['patient_name'])) {
            $bodyParameters[] = ['type' => 'text', 'text' => $message['patient_name']];
        }
        if (! empty($message['practitioner_name'])) {
            $bodyParameters[] = ['type' => 'text', 'text' => $message['practitioner_name']];
        }

        if (! empty($bodyParameters)) {
            $components[] = [
                'type' => 'body',
                'parameters' => $bodyParameters,
            ];
        }

        // Send template message for each document
        foreach ($documents as $document) {
            $documentComponents = $components;

            // Add header component with document
            $documentComponents[] = [
                'type' => 'header',
                'parameters' => [
                    [
                        'type' => 'document',
                        'document' => [
                            'link' => $document['url'],
                            'filename' => $document['filename'] ?? 'documento.pdf',
                        ],
                    ],
                ],
            ];

            // Send template
            $result = $this->whatsAppService->sendTemplate(
                $phoneNumber,
                $templateName,
                $documentComponents,
                'es'
            );

            if ($result) {
                Log::info('Template message sent successfully', [
                    'template' => $templateName,
                    'document_type' => $document['type'] ?? 'unknown',
                    'message_id' => $result['message_id'] ?? null,
                ]);
            } else {
                Log::error('Failed to send template message', [
                    'template' => $templateName,
                    'document_type' => $document['type'] ?? 'unknown',
                ]);
            }
        }
    }
}
