<?php

// Test environment (local phpunit and GitHub CI)
return function (array $settings): array {
    $settings['error']['display_error_details'] = true;

    // Database
    $settings['db']['adapter'] = 'pgsql';
    $settings['db']['host'] = getenv('DB_HOST') ?: '127.0.0.1';
    $settings['db']['port'] = (int)(getenv('DB_PORT') ?: 5432);
    $settings['db']['database'] = getenv('DB_NAME') ?: 'release_notification_test';
    $settings['db']['username'] = getenv('DB_USER') ?: 'postgres';
    $settings['db']['password'] = getenv('DB_PASS') ?: 'postgres';

    return $settings;
};
