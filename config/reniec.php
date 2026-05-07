<?php

return [
    /*
    |--------------------------------------------------------------------------
    | RENIEC API Configuration
    |--------------------------------------------------------------------------
    |
    | This configuration is used to connect to the RENIEC/SUNAT API service
    | for DNI and RUC validation in Peru.
    |
    */

    'api_url' => env('RENIEC_API_URL', 'https://dniruc.apisperu.com/api/v1'),

    'token' => env('RENIEC_API_TOKEN'),

    'timeout' => env('RENIEC_API_TIMEOUT', 10),
];
