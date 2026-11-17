<?php

return [
    'default' => env('MAIL_MAILER', 'log'),
    'mailers' => [
        'log' => ['transport' => 'log'],
        'array' => ['transport' => 'array'],
    ],
    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'hello@nusaboard.test'),
        'name' => env('MAIL_FROM_NAME', env('APP_NAME', 'NusaBoard')),
    ],
];
