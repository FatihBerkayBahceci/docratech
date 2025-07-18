<?php

return [

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'http://localhost:3000',
        'http://localhost:5173', 
        'http://127.0.0.1:8000',
        'http://localhost:8000',
    ],

    'allowed_origins_patterns' => [
        'http://localhost:*',
        'https://localhost:*',
        'http://127.0.0.1:*',
        'https://127.0.0.1:*',
    ],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

]; 