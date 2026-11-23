<?php

return [
    'rate_limits' => [
        'login' => [
            'attempts' => 5,
            'period_minutes' => 1,
        ],
        'password_reset' => [
            'attempts' => 3,
            'period_minutes' => 1,
        ],
        'register' => [
            'attempts' => 5,
            'period_minutes' => 1,
        ],
        'email_verification' => [
            'attempts' => 6,
            'period_minutes' => 1,
        ],
        'consultation' => [
            'attempts' => 5,
            'period_minutes' => 60,
        ],
        'consultation_replies' => [
            'attempts' => 20,
            'period_minutes' => 1,
        ],
        'chatbot' => [
            'attempts' => 30,
            'period_minutes' => 60,
        ],
    ],
];
