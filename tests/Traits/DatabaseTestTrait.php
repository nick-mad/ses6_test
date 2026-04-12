<?php

namespace App\Test\Traits;

use PDO;
use RuntimeException;

trait DatabaseTestTrait
{
    protected function setUpDatabase(): void
    {
        $settings = $this->container->get('settings')['db'];

        $adapter = $settings['adapter'] ?? 'mysql';
        $host = $settings['host'];
        $dbName = $settings['database'];
        $port = (int)$settings['port'];
        $username = $settings['username'];
        $password = $settings['password'];
        $charset = $settings['encoding'] ?? 'utf8mb4';
        $flags = $settings['options'] ?? [];

        if ($adapter === 'pgsql') {
            $dsn = "pgsql:host=$host;port=$port;dbname=postgres";
            $pdo = new PDO($dsn, $username, $password, $flags);
            $pdo->exec("DROP DATABASE IF EXISTS \"{$dbName}\" (FORCE)");
            $pdo->exec("CREATE DATABASE \"{$dbName}\"");
        } else {
            // Connect without database name to create it
            $dsn = "mysql:host=$host;port=$port;charset=$charset";
            $pdo = new PDO($dsn, $username, $password, $flags);
            $pdo->exec("DROP DATABASE IF EXISTS `{$dbName}`");
            $pdo->exec("CREATE DATABASE `{$dbName}`");
        }

        $this->runMigrations();
    }

    protected function runMigrations(): void
    {
        $settings = $this->container->get('settings')['db'];
        $phinxPath = __DIR__ . '/../../vendor/bin/phinx';
        $environment = 'testing';
        $dbName = $settings['database'];
        $dbHost = $settings['host'];
        $dbUser = $settings['username'];
        $dbPass = $settings['password'];
        $dbPort = $settings['port'];
        $envVars = "DB_NAME={$dbName} DB_HOST={$dbHost} DB_USER={$dbUser}"
            . " DB_PASS={$dbPass} DB_PORT={$dbPort}";

        $command = "{$envVars} {$phinxPath} migrate -e {$environment}";
        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            throw new RuntimeException("Phinx migration failed: " . implode("\n", $output));
        }

        // Run seeds
        $command = "{$envVars} {$phinxPath} seed:run -e {$environment}";
        exec($command, $output, $returnVar);
        if ($returnVar !== 0) {
            throw new RuntimeException("Phinx seeding failed: " . implode("\n", $output));
        }
    }
}
