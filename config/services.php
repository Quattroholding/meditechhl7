<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'twilio' => [
        'sid' => env('TWILIO_SID'),
        'token' => env('TWILIO_TOKEN'),
        'whatsapp_from' => env('TWILIO_WHATSAPP_FROM'),
        'testing_mode' => env('TWILIO_TESTING_MODE', false),
        'testing_patient_whatsapp' => env('TWILIO_TESTING_PATIENT_WHATSAPP'),
    ],

    'n8n' => [
        'webhook_url' => env('N8N_WEBHOOK_URL', 'https://n8n.meditecpty.com/webhook/1e35ec4b-2813-4d1b-a37d-919c8b6a66d1'),
        'webhook_test_url' => env('N8N_WEBHOOK_TEST_URL', 'https://n8n.meditecpty.com/webhook-test/1e35ec4b-2813-4d1b-a37d-919c8b6a66d1'),
        'survey_webhook_url' => env('N8N_SURVEY_WEBHOOK_URL'),
        'survey_whatsapp_enabled' => env('N8N_SURVEY_WHATSAPP_ENABLED', false),
        'token' => env('N8N_TOKEN'),
        'testing_mode' => env('N8N_TESTING_MODE', false),
        'testing_phone' => env('N8N_TESTING_PHONE'),
    ],

    'turnstile' => [
        'public_key' => env('TURNSTILE_PUBLIC_KEY'),
        'secret_key' => env('TURNSTILE_SECRET_KEY'),
    ],

];
