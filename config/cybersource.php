<?php

return [
    'merchant_id' => env('CYBERSOURCE_MERCHANT_ID'),
    'key_id' => env('CYBERSOURCE_KEY_ID'),
    'secret_key' => env('CYBERSOURCE_SECRET_KEY'),
    'shared_secret' => env('CYBERSOURCE_SHARED_SECRET'),

    'base_url' => env('CYBERSOURCE_ENV') === 'production'
        ? 'https://api.cybersource.com'
        : 'https://apitest.cybersource.com',

    // Set to true when Token Management Service (TMS) is enabled in your account
    // Enables saving payment tokens for automatic recurring charges
    'use_tms' => env('CYBERSOURCE_USE_TMS', false),
];
