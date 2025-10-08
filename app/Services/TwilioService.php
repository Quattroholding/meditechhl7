<?php

namespace App\Services;

use Twilio\Rest\Client;

class TwilioService
{
    protected Client $client;

    protected string $whatsappFrom;

    public function __construct()
    {
        $this->client = new Client(
            config('services.twilio.sid'),
            config('services.twilio.token')
        );

        $this->whatsappFrom = config('services.twilio.whatsapp_from');
    }

    /**
     * Send a WhatsApp message using Twilio
     *
     * @param  string  $to  The recipient's phone number (e.g., +50760016054)
     * @param  string  $message  The message body
     */
    public function sendWhatsAppMessage(string $to, string $message): \Twilio\Rest\Api\V2010\Account\MessageInstance
    {
        // Ensure the recipient number is in WhatsApp format
        $toWhatsApp = str_starts_with($to, 'whatsapp:') ? $to : 'whatsapp:'.$to;

        return $this->client->messages->create(
            $toWhatsApp,
            [
                'from' => $this->whatsappFrom,
                'body' => $message,
            ]
        );
    }

    /**
     * Send a WhatsApp message using a content template
     *
     * @param  string  $to  The recipient's phone number (e.g., +50760016054)
     * @param  string  $contentSid  The Content SID from Twilio
     * @param  array  $contentVariables  Variables to replace in the template
     */
    public function sendWhatsAppTemplate(string $to, string $contentSid, array $contentVariables = []): \Twilio\Rest\Api\V2010\Account\MessageInstance
    {
        // Ensure the recipient number is in WhatsApp format
        $toWhatsApp = str_starts_with($to, 'whatsapp:') ? $to : 'whatsapp:'.$to;

        $params = [
            'from' => $this->whatsappFrom,
            'contentSid' => $contentSid,
        ];

        if (! empty($contentVariables)) {
            $params['contentVariables'] = json_encode($contentVariables);
        }

        return $this->client->messages->create($toWhatsApp, $params);
    }
}
