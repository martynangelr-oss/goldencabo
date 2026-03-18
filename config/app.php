<?php

return [
    'name' => env('APP_NAME', 'Golden Cabo Transportation'),
    'env' => env('APP_ENV', 'production'),
    'debug' => (bool) env('APP_DEBUG', false),
    'url' => env('APP_URL', 'http://localhost'),
    'asset_url' => env('ASSET_URL'),
    'timezone' => 'America/Mazatlan',
    'locale' => 'es',
    'fallback_locale' => 'en',
    'faker_locale' => 'es_MX',
    'key' => env('APP_KEY'),
    'cipher' => 'AES-256-CBC',
    'maintenance' => ['driver' => 'file'],
    'admin_email' => env('ADMIN_EMAIL', 'goldencabotransportation@gmail.com'),
];
