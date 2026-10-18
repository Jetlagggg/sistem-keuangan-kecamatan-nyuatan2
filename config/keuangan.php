<?php

return [
    'backup' => [
        'path' => env('BACKUP_PATH', storage_path('app/backups')),
        'keep_days' => (int) env('BACKUP_KEEP_DAYS', 14),
    ],

    'security' => [
        'max_login_attempts' => (int) env('LOGIN_MAX_ATTEMPTS', 5),
        'lockout_minutes' => (int) env('LOGIN_LOCKOUT_MINUTES', 15),
    ],
];
