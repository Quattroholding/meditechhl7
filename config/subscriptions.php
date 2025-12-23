<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Grace Period Days
    |--------------------------------------------------------------------------
    |
    | Number of days after invoice due date before suspending the subscription
    |
    */
    'grace_period_days' => env('SUBSCRIPTION_GRACE_PERIOD_DAYS', 7),

    /*
    |--------------------------------------------------------------------------
    | Reminder Days Before Due Date
    |--------------------------------------------------------------------------
    |
    | Days before due date to send payment reminders
    |
    */
    'reminder_days_before' => [7, 3, 1],

    /*
    |--------------------------------------------------------------------------
    | Default Trial Days
    |--------------------------------------------------------------------------
    |
    | Default number of days for trial subscriptions
    |
    */
    'default_trial_days' => env('SUBSCRIPTION_DEFAULT_TRIAL_DAYS', 0),

    /*
    |--------------------------------------------------------------------------
    | Expiration After Suspension Days
    |--------------------------------------------------------------------------
    |
    | Days after suspension before expiring the subscription permanently
    |
    */
    'expiration_after_suspension_days' => env('SUBSCRIPTION_EXPIRATION_DAYS', 30),

    /*
    |--------------------------------------------------------------------------
    | Invoice Number Format
    |--------------------------------------------------------------------------
    |
    | Format for generating invoice numbers
    | Available placeholders: {YEAR}, {MONTH}, {SEQUENCE}
    |
    */
    'invoice_number_format' => 'SUB-{YEAR}{MONTH}-{SEQUENCE}',

    /*
    |--------------------------------------------------------------------------
    | Referral Code Prefix
    |--------------------------------------------------------------------------
    |
    | Prefix for generating referral codes
    |
    */
    'referral_code_prefix' => env('REFERRAL_CODE_PREFIX', 'REF'),

    /*
    |--------------------------------------------------------------------------
    | Default Referrer Reward
    |--------------------------------------------------------------------------
    |
    | Default reward configuration for the client who refers
    | Type: credit - $5 credit per referral (uses ReferrerRewardType enum)
    | Credits accumulate and are applied to next invoices
    |
    */
    'default_referrer_reward' => [
        'type' => 'credit',
        'value' => 5.00,
        'invoices' => 1,
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Referred Benefit
    |--------------------------------------------------------------------------
    |
    | Default benefit configuration for the referred client
    | Type: fixed_amount - $0 (uses DiscountType enum)
    | The referred client gets no discount (only referrer gets credit)
    |
    */
    'default_referred_benefit' => [
        'type' => 'fixed_amount',
        'value' => 0,
        'invoices' => 0,
    ],

    /*
    |--------------------------------------------------------------------------
    | Enabled Payment Methods
    |--------------------------------------------------------------------------
    |
    | Payment methods available for subscription invoices
    |
    */
    'enabled_payment_methods' => [
        'ach',
        'yappy',
        'bank_transfer',
        'cash',
    ],

    /*
    |--------------------------------------------------------------------------
    | Payment Gateways
    |--------------------------------------------------------------------------
    |
    | Configuration for payment gateway integrations
    |
    */
    'payment_gateways' => [
        'stripe' => [
            'enabled' => env('STRIPE_ENABLED', false),
            'key' => env('STRIPE_KEY'),
            'secret' => env('STRIPE_SECRET'),
        ],
        'yappy' => [
            'enabled' => env('YAPPY_ENABLED', false),
            'merchant_id' => env('YAPPY_MERCHANT_ID'),
            'api_key' => env('YAPPY_API_KEY'),
        ],
    ],
];
