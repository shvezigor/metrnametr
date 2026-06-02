<?php

return [
    'mailForAdmin' => env('MAIL_FOR_ADMIN', 'admin@admin.com'),
    'mailForCompany' => env('MAIL_FOR_COMPANY', 'metrnametr@ukr.net'),
    'google' => [
        'map' => [
            'key' => env('GOOGLE_MAP_KEY', ''),
            'lat' => env('GOOGLE_MAP_LAT', 0),
            'lng' => env('GOOGLE_MAP_LNG', 0),
        ],
    ]
];
