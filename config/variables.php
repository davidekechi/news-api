<?php

declare(strict_types=1);

return [
    'pagination' => [
        'default_per_page' => env('DEFAULT_PER_PAGE', 15)
    ],

    'rate_limiting' => [
        'api'    => env('RATE_LIMIT_API', 1000000),
        'short'  => env('RATE_LIMIT_SHORT', 1000000),
        'medium' => env('RATE_LIMIT_MEDIUM', 1000000),
        'long'   => env('RATE_LIMIT_LONG', 1000000),
    ]
];
