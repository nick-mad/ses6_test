<?php

// Github Action environment

return function (array $settings): array {
    $settings = (require __DIR__ . '/local.test.php')($settings);

    // Database
    $settings['db']['adapter'] = 'pgsql';
    $settings['db']['host'] = '127.0.0.1';
    $settings['db']['port'] = 5432;
    $settings['db']['database'] = 'release_notification';
    $settings['db']['username'] = 'postgres';
    $settings['db']['password'] = 'postgres';

    return $settings;
};
