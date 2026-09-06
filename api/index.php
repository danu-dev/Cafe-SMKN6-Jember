<?php

// Pastikan direktori writeable /tmp tersedia di serverless Vercel
$storagePaths = [
    '/tmp/views',
    '/tmp/cache',
    '/tmp/sessions',
    '/tmp/logs',
    '/tmp/database',
];

foreach ($storagePaths as $path) {
    if (! is_dir($path)) {
        @mkdir($path, 0755, true);
    }
}

// Inisialisasi SQLite database di /tmp
$tmpDbPath = '/tmp/database/database.sqlite';
$seedDbPath = __DIR__ . '/../database/database.sqlite';

if (! file_exists($tmpDbPath) || filesize($tmpDbPath) === 0) {
    if (file_exists($seedDbPath)) {
        @copy($seedDbPath, $tmpDbPath);
    } else {
        @touch($tmpDbPath);
    }
}

// Set environment database ke /tmp
putenv("DB_CONNECTION=sqlite");
putenv("DB_DATABASE={$tmpDbPath}");
$_ENV['DB_CONNECTION'] = 'sqlite';
$_ENV['DB_DATABASE'] = $tmpDbPath;
$_SERVER['DB_CONNECTION'] = 'sqlite';
$_SERVER['DB_DATABASE'] = $tmpDbPath;

// Forward request to Laravel public/index.php
require __DIR__ . '/../public/index.php';
