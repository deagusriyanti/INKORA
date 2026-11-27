<?php

return [
    'grade' => env('APP_GRADE', 'basic'),

    'grade_labels' => [
        'basic'    => 'Basic Edition',
        'standard' => 'Standard Edition',
        'premium'  => 'Premium Edition',
    ],

    'features' => [
        'basic' => [
            'core_admin',
            'basic_reports',
        ],
        'standard' => [
            'core_admin',
            'basic_reports',
            'dp_payment',
            'billing_reminders',
            'unpaid_reports',
        ],
        'premium' => [
            'core_admin',
            'basic_reports',
            'dp_payment',
            'billing_reminders',
            'unpaid_reports',
            'production_tracking',
            'delivery_tracking',
            'operational_reports',
        ],
    ],
];