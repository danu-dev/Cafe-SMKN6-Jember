<?php

return [
    'secret_key' => env('XENDIT_SECRET_KEY', ''),
    'public_key' => env('XENDIT_PUBLIC_KEY', ''),
    'webhook_token' => env('XENDIT_WEBHOOK_TOKEN', ''),
    'base_url' => env('XENDIT_BASE_URL', 'https://api.xendit.co'),
    'invoice_duration' => (int) env('XENDIT_INVOICE_DURATION', 86400), // 24 jam dalam detik
];
