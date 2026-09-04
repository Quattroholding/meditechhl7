<?php

return [
    'enabled' => env('DEBUG_LOGIN_ENABLED', env('APP_ENV') !== 'production'),

    'trusted_ips' => [
        '127.0.0.1',
        '::1',
        '200.12.208.98',
        '200.75.229.130',
        '200.8.97.119',
        '186.14.57.59',
        '190.34.252.201',
        // Agregar más IPs según necesidad
    ],

    // Permitir configurar IPs adicionales desde .env
    'additional_ips' => array_filter(explode(',', env('DEBUG_TRUSTED_IPS', ''))),

    'ip_oficina_san_francisco' => '200.12.208.98',
];
