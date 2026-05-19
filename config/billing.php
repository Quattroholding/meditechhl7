<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Tax Configuration
    |--------------------------------------------------------------------------
    |
    | Configure whether to apply tax to invoices and the tax rate.
    | By default, tax is disabled (false).
    |
    */

    'tax_enabled' => env('BILLING_TAX_ENABLED', false),

    'tax_rate' => env('BILLING_TAX_RATE', 0.07), // Default 7% ITBMS for Panama

];
