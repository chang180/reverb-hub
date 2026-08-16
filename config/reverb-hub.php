<?php

return [

    'api_url' => env('REVERB_API_URL', 'http://127.0.0.1:8080'),

    'admin' => [
        'name' => env('ADMIN_NAME', 'Admin'),
        'email' => env('ADMIN_EMAIL', 'admin@reverb-hub.test'),
        'password' => env('ADMIN_PASSWORD', 'password'),
    ],

];
