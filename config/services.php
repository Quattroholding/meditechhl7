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

    'jitsi' => [
        'domain' => env('JITSI_DOMAIN', 'meet.jit.si'),
        'app_id' => env('JITSI_APP_ID'),
        'app_secret' => env('JITSI_APP_SECRET'),
        'key_id' => env('JITSI_KEY_ID'),
    ],

    'zoom' => [
        'account_id' => env('ZOOM_ACCOUNT_ID'),
        'client_id' => env('ZOOM_CLIENT_ID'),
        'client_secret' => env('ZOOM_CLIENT_SECRET'),
        'host_user_id' => env('ZOOM_HOST_USER_ID'),
        'webhook_secret' => env('ZOOM_WEBHOOK_SECRET_TOKEN'),
        'data_center' => env('ZOOM_DATA_CENTER', 'US'),
        'api_base_url' => env('ZOOM_API_BASE_URL', 'https://zoom.us/v2'),
    ],
    'yappy_test' => [
        'merchant_id' => env('YAPPY_MERCHANT_ID', 'meet.yappy.com'),
        'base_url' => env('YAPPY_API_BASE', 'https://api.yappy.com'),
        'secret_key' => env('YAPPY_SECRET_KEY'),
    ],
    'yappy' => [
        'merchant_id' => env('YAPPY_MERCHANT_ID_PROD', 'meet.yappy.com'),
        'base_url' => env('YAPPY_API_BASE_PROD', 'https://api.yappy.com'),
        'secret_key' => env('YAPPY_SECRET_KEY_PROD'),
    ],
    'meta' => [
        'whatsapp_phone_number_id' => env('META_WHATSAPP_PHONE_NUMBER_ID'),
        'whatsapp_access_token' => env('META_WHATSAPP_ACCESS_TOKEN'),
        'whatsapp_business_account_id' => env('META_WHATSAPP_BUSINESS_ACCOUNT_ID'),
        'testing_mode' => env('META_TESTING_MODE', false),
        'testing_phone' => env('META_TESTING_PHONE'),
        'webhook_verify_token' => env('META_WEBHOOK_VERIFY_TOKEN', 'meditech_whatsapp_webhook_2026'),
    ],

    'neopayments' => [
        'enabled' => env('NEOPAYMENTS_ENABLED', false),
        'host' => env('NEOPAYMENTS_HOST', 'https://api.neopayments.com'),
        'client_id' => env('NEOPAYMENTS_CLIENT_ID'),
        'client_secret' => env('NEOPAYMENTS_CLIENT_SECRET'),
        'retry_attempts' => env('NEOPAYMENTS_RETRY_ATTEMPTS', 2),
        '3ds_enabled' => env('NEOPAYMENTS_3DS_ENABLED', false),
        'webhook_base_path' => env('WEBHOOK_BASE_PATH'),
    ],

    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
        'api_url' => env('OPENAI_API_URL', 'https://api.openai.com/v1'),
        'timeout' => env('OPENAI_TIMEOUT', 60),
    ],

    'claude' => [
        'api_key' => env('CLAUDE_API_KEY'),
        'api_url' => env('CLAUDE_API_URL', 'https://api.anthropic.com/v1'),
        'api_version' => env('CLAUDE_API_VERSION', '2023-06-01'),
        'default_model' => env('CLAUDE_DEFAULT_MODEL', 'claude-sonnet-4-6'),
        'default_max_tokens' => env('CLAUDE_DEFAULT_MAX_TOKENS', 4096),
        'timeout' => env('CLAUDE_TIMEOUT', 60),
        'diagnostics_suggestions_enabled' => env('CLAUDE_DIAGNOSTICS_SUGGESTIONS_ENABLED', true),
        'voice_dictation_enabled' => env('CLAUDE_VOICE_DICTATION_ENABLED', true),
        'voice_dictation_max_duration' => env('CLAUDE_VOICE_DICTATION_MAX_DURATION', 300),
        'voice_dictation_max_file_size' => env('CLAUDE_VOICE_DICTATION_MAX_FILE_SIZE', 10485760),
    ],

    'dropbox' => [
        'app_key' => env('DROPBOX_APP_KEY'),
        'app_secret' => env('DROPBOX_APP_SECRET'),
    ],

    'microsoft' => [
        'tenant_id' => env('MICROSOFT_TENANT_ID'),
        'client_id' => env('MICROSOFT_CLIENT_ID'),
        'client_secret' => env('MICROSOFT_CLIENT_SECRET'),
        'mailbox_email' => env('MICROSOFT_MAILBOX_EMAIL', 'notificaciones@meditecpty.com'),
        'save_to_sent_items' => env('MICROSOFT_SAVE_TO_SENT_ITEMS', true),
    ],

    'nightwatch' => [
        'webhook_secret' => env('NIGHTWATCH_WEBHOOK_SECRET'),
        'min_priority_for_ai' => env('NIGHTWATCH_MIN_PRIORITY_FOR_AI', 'medium'),
        'ai_enabled' => env('NIGHTWATCH_AI_ENABLED', true),
        'alert_emails' => env('NIGHTWATCH_ALERT_EMAILS'),
    ],

];
