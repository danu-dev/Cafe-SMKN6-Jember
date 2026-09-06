<?php

// Pastikan direktori writeable /tmp tersedia di serverless Vercel
$storagePaths = [
    '/tmp/views',
    '/tmp/cache',
    '/tmp/sessions',
    '/tmp/logs',
];

foreach ($storagePaths as $path) {
    if (! is_dir($path)) {
        @mkdir($path, 0755, true);
    }
}

// Forward request to Laravel public/index.php
require __DIR__ . '/../public/index.php';
