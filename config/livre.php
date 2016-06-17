<?php
return [
    'providers' => ['google'],
    'services' => [
        'google' => [
            'driver' => \Znck\Livre\Drivers\GoogleBooks::class,
            'key' => env('LIVRE_GOOGLE_API_KEY', ''),
        ],
    ]
];