<?php

// Detect environment
$_ENV['APP_ENV'] ??= $_SERVER['APP_ENV'] ?? getenv('APP_ENV') ?: 'dev';

// Load default settings
$settings = require __DIR__ . '/defaults.php';

// Overwrite default settings with environment specific local settings
$configFile = __DIR__ . sprintf('/local.%s.php', $_ENV['APP_ENV']);
if (file_exists($configFile)) {
    $local = require $configFile;
    if (is_callable($local)) {
        $settings = $local($settings);
    }
}

// Apply environment variables
if (($v = getenv('DB_HOST')) !== false) {
    $settings['db']['host'] = $v;
}
if (($v = getenv('DB_PORT')) !== false) {
    $settings['db']['port'] = (int)$v;
}
if (($v = getenv('DB_NAME')) !== false) {
    $settings['db']['database'] = $v;
}
if (($v = getenv('DB_USER')) !== false) {
    $settings['db']['username'] = $v;
}
if (($v = getenv('DB_PASS')) !== false) {
    $settings['db']['password'] = $v;
}

// GitHub
if (($v = getenv('GITHUB_TOKEN')) !== false) {
    $settings['github']['token'] = $v;
}

// Mail
if (($v = getenv('MAIL_DRIVER'))     !== false) {
    $settings['mail']['driver']     = $v;
}
if (($v = getenv('MAIL_DSN'))        !== false) {
    $settings['mail']['dsn']        = $v;
}
if (($v = getenv('MAIL_FROM_EMAIL')) !== false) {
    $settings['mail']['from_email'] = $v;
}
if (($v = getenv('MAIL_FROM_NAME'))  !== false) {
    $settings['mail']['from_name']  = $v;
}
if (($v = getenv('MAIL_BASE_URL'))   !== false) {
    $settings['mail']['base_url']   = $v;
}

return $settings;
