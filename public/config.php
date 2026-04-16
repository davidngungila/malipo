<?php

/**
 * ClickPesa API Configuration
 * 
 * This file contains all the configuration settings for the ClickPesa payment system
 */

return [
    'clickpesa' => [
        'api_base_url' => 'https://api.clickpesa.com/third-parties',
        'client_id' => 'IDgD7WpY3jG9pyL61YjkWRIxeA0AGW8w', // FEEDTAN PAY v2 Client ID
        'api_key' => 'SKeAAUccbibncmlb9kBh8YuIr1i93m3JXOJGJM3zmE',     // FEEDTAN PAY v2 API Key
        'timeout' => 30,
        'currency' => 'TZS',
        
        // Payment settings
        'payment' => [
            'default_amount' => 1000,
            'min_amount' => 100,
            'max_amount' => 1000000,
        ],
        
        // Callback settings
        'callback' => [
            'url' => 'http://192.168.3.163/clickpesa/callback.php',
            'secret_key' => '<your-callback-secret>', // Optional: for webhook security
        ],
    ],
    
    // Database configuration (if needed for storing transactions)
    'database' => [
        'host' => 'localhost',
        'dbname' => 'clickpesa',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8mb4',
    ],
    
    // Application settings
    'app' => [
        'name' => 'ClickPesa Payment System',
        'timezone' => 'Africa/Dar_es_Salaam',
        'debug' => true,
    ]
];
