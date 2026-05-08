<?php

return [
    'enabled' => env('API_DOCS_ENABLED', env('APP_ENV') !== 'production'),

    'trusted_ips' => [
        //'127.0.0.1',
        '::1',
        '200.12.208.98',
        '200.75.229.130',
        '200.8.97.119',
        '186.14.57.59',
        // Agregar más IPs según necesidad
    ],

    // Permitir configurar IPs adicionales desde .env
    'additional_ips' => array_filter(explode(',', env('API_DOCS_TRUSTED_IPS', ''))),
];
