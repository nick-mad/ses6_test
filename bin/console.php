<?php

use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArgvInput;

require_once __DIR__ . '/../vendor/autoload.php';

$env = new ArgvInput()->getParameterOption(['--env', '-e'], 'dev');

if ($env) {
    $_ENV['APP_ENV'] = $env;
    putenv("APP_ENV=$env");
}

try {
    /** @var ContainerInterface $container */
    $container = new ContainerBuilder()
        ->addDefinitions(__DIR__ . '/../config/container.php')
        ->build();

    /** @var Application $application */
    $application = $container->get(Application::class);

    exit($application->run());
} catch (Throwable $exception) {
    echo $exception->getMessage() . PHP_EOL;
    exit(1);
}
