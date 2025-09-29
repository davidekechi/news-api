<?php

declare(strict_types=1);

return [
    'pagination' => [
        'default_per_page' => env('DEFAULT_PER_PAGE', 15)
    ],

    'rate_limiting' => [
        'api' => env('RATE_LIMIT_API', 120),
    ]
];
