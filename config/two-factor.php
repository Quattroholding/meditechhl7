<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Two-Factor Authentication Settings
    |--------------------------------------------------------------------------
    |
    | Configure two-factor authentication behavior for the application.
    |
    */

    'enabled' => env('TWO_FACTOR_ENABLED', true),

    'issuer' => env('TWO_FACTOR_ISSUER', 'Meditech2'),

    'digits' => 6,

    'window' => 1, // ±30 seconds for clock drift tolerance

    'recovery_codes_count' => 10,

    'qr_code_size' => 200,

    /*
    |--------------------------------------------------------------------------
    | Email Backup Code Settings
    |--------------------------------------------------------------------------
    |
    | Configure email backup codes as a fallback authentication method.
    |
    */

    'email_backup' => [
        'enabled' => true,
        'expiry' => 15, // minutes
        'code_length' => 6,
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Configure rate limiting for 2FA challenge attempts.
    |
    */

    'max_attempts' => 5,

    'lockout_time' => 15, // minutes

];
