<?php

return [
    /*
    |--------------------------------------------------------------------------
    | SMS API Configuration
    |--------------------------------------------------------------------------
    |
    | Here you can configure your SMS API connection
    |
    */

    // Your API key for authentication
    'api_key' => env('SMS_SENDER_API_KEY', ''),

    // The base URL for the SMS API
    'base_url' => env('SMS_SENDER_BASE_URL', 'https://api.example.com'),

    // Default settings for SMS messages
    'defaults' => [
        'prefix' => '998',
        'operator' => 'default'
    ]
];
