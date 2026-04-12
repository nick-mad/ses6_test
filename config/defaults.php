<?php

// Application default settings

// Error reporting
error_reporting(0);
ini_set('display_errors', '0');
ini_set('display_startup_errors', '0');

// Timezone
date_default_timezone_set('Europe/Kyiv');

$settings = [];

// Error handler
$settings['error'] = [
    // Should be set to false for the production environment
    'display_error_details' => false,
];

// Logger settings
$settings['logger'] = [
    // Log file location
    'path' => __DIR__ . '/../logs',
    // Default log level
    'level' => Psr\Log\LogLevel::DEBUG,
];

// Database settings
$settings['db'] = [
    'adapter' => 'pgsql',
    'host' => 'localhost',
    'port' => 5432,
    'database' => 'release_notification',
    'username' => 'postgres',
    'password' => '',
    // PDO options
    'options' => [
        PDO::ATTR_PERSISTENT => false,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => true,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ],
];

// GitHub settings
$settings['github'] = [
    'token' => null,
];

// Templates settings
$settings['templates'] = [
    'path' => __DIR__ . '/../templates',
];

// Mail settings
$settings['mail'] = [
    'driver' => 'log', // 'log' or 'smtp'
    'dsn' => null, // Use this for symfony/mailer (e.g., smtp://user:pass@host:port)
    'from_email' => 'noreply@example.com',
    'from_name' => 'Release Notifier',
    'base_url' => 'http://localhost:8080',
];

return $settings;
