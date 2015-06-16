<?php
return [
    'google' => [
        'provider' => \Znck\Livre\Providers\GoogleBooksProvider::class,
        'key'      => env('LIVRE_GOOGLE_API_KEY', ''),
    ]
];