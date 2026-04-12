<?php

use App\Application\Subscription\Service\ScannerService;
use App\Application\Subscription\Service\ScannerServiceInterface;
use App\Application\Subscription\Service\SubscribeService;
use App\Application\Subscription\Service\SubscribeServiceInterface;
use App\Domain\Subscription\Client\GithubClientInterface;
use App\Domain\Subscription\Provider\SubscriptionProviderInterface;
use App\Domain\Subscription\Repository\SubscriptionRepositoryInterface;
use App\Domain\Subscription\Service\NotifierInterface;
use App\Infrastructure\Client\GuzzleGithubClient;
use App\Infrastructure\Persistence\PdoSubscriptionRepository;
use App\Infrastructure\Provider\SubscriptionProvider;
use App\Infrastructure\Service\LogNotifier;
use App\Infrastructure\Service\SmtpNotifier;
use App\Presentation\Console\Subscription\ScanCommand;
use App\Shared\Middleware\ApiKeyMiddleware;
use App\Shared\Middleware\ExceptionMiddleware;
use App\Shared\Renderer\JsonRenderer;
use App\Shared\Renderer\TemplateRenderer;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Log\LoggerInterface;
use Selective\BasePath\BasePathMiddleware;
use Slim\App;
use Slim\Factory\AppFactory;
use Slim\Interfaces\RouteParserInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Transport;

use function DI\get;

return [
    // Application settings
    'settings' => fn() => require __DIR__ . '/settings.php',

    App::class => function (ContainerInterface $container) {
        $app = AppFactory::createFromContainer($container);

        // Register routes
        (require __DIR__ . '/routes.php')($app);

        // Register middleware
        (require __DIR__ . '/middleware.php')($app);

        return $app;
    },

    // HTTP factories
    ResponseFactoryInterface::class => function (ContainerInterface $container) {
        return $container->get(Psr17Factory::class);
    },

    ServerRequestFactoryInterface::class => function (ContainerInterface $container) {
        return $container->get(Psr17Factory::class);
    },

    StreamFactoryInterface::class => function (ContainerInterface $container) {
        return $container->get(Psr17Factory::class);
    },

    UploadedFileFactoryInterface::class => function (ContainerInterface $container) {
        return $container->get(Psr17Factory::class);
    },

    UriFactoryInterface::class => function (ContainerInterface $container) {
        return $container->get(Psr17Factory::class);
    },

    // The Slim RouterParser
    RouteParserInterface::class => function (ContainerInterface $container) {
        return $container->get(App::class)->getRouteCollector()->getRouteParser();
    },

    BasePathMiddleware::class => function (ContainerInterface $container) {
        return new BasePathMiddleware($container->get(App::class));
    },

    LoggerInterface::class => function (ContainerInterface $container) {
        $settings = $container->get('settings')['logger'];
        $logger = new Logger('app');

        $filename = sprintf('%s/app.log', $settings['path']);
        $level = $settings['level'];
        $rotatingFileHandler = new RotatingFileHandler($filename, 0, $level, true, 0777);
        $rotatingFileHandler->setFormatter(new LineFormatter(null, null, false, true));
        $logger->pushHandler($rotatingFileHandler);

        return $logger;
    },

    TemplateRenderer::class => function (ContainerInterface $container) {
        $settings = $container->get('settings')['templates'] ?? [];
        $path = $settings['path'] ?? (__DIR__ . '/../templates');
        return new TemplateRenderer($path);
    },

    ExceptionMiddleware::class => function (ContainerInterface $container) {
        $settings = $container->get('settings')['error'];

        return new ExceptionMiddleware(
            $container->get(ResponseFactoryInterface::class),
            $container->get(JsonRenderer::class),
            $container->get(LoggerInterface::class),
            (bool)$settings['display_error_details'],
        );
    },

    ApiKeyMiddleware::class => function (ContainerInterface $container) {
        $settings = $container->get('settings')['api_key'] ?? 'demo-api-key';

        return new ApiKeyMiddleware(
            $container->get(ResponseFactoryInterface::class),
            $container->get(JsonRenderer::class),
            (string)$settings
        );
    },

    PDO::class => function (ContainerInterface $container) {
        $settings = $container->get('settings')['db'];
        $flags = $settings['options'];

        $adapter = $settings['adapter'] ?? 'mysql';
        $host = $settings['host'];
        $dbname = $settings['database'];
        $username = $settings['username'];
        $password = $settings['password'];
        $charset = $settings['encoding'] ?? 'utf8mb4';
        $port = (int)$settings['port'];

        if ($adapter === 'pgsql') {
            $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
        } else {
            $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=$charset";
        }

        return new PDO($dsn, $username, $password, $flags);
    },

    ClientInterface::class => function (ContainerInterface $container) {
        $githubSettings = $container->get('settings')['github'] ?? [];
        $token = $githubSettings['token'] ?? null;

        $config = [
            'base_uri' => 'https://api.github.com/',
            'timeout' => 5.0,
            'headers' => [
                'Accept' => 'application/vnd.github.v3+json',
                'User-Agent' => 'GitHub-Release-Notification',
            ],
        ];

        if ($token) {
            $config['headers']['Authorization'] = "token $token";
        }

        return new Client($config);
    },

    GithubClientInterface::class => DI\get(GuzzleGithubClient::class),

    MailerInterface::class => function (ContainerInterface $container) {
        $settings = $container->get('settings')['mail'];
        $transport = Transport::fromDsn($settings['dsn'] ?? 'null://default');

        return new Mailer($transport);
    },

    NotifierInterface::class => function (ContainerInterface $container) {
        $settings = $container->get('settings')['mail'];
        $driver = $settings['driver'] ?? 'log';

        if ($driver === 'smtp') {
            return new SmtpNotifier(
                $container->get(LoggerInterface::class),
                $container->get(MailerInterface::class),
                $settings
            );
        }

        return new LogNotifier($container->get(LoggerInterface::class));
    },

    SubscribeServiceInterface::class => get(SubscribeService::class),
    ScannerServiceInterface::class => get(ScannerService::class),
    SubscriptionRepositoryInterface::class => get(PdoSubscriptionRepository::class),
    SubscriptionProviderInterface::class => get(SubscriptionProvider::class),

    Application::class => function (ContainerInterface $container) {
        $application = new Application();
        $application->setName('Console Application');
        $application->setVersion('1.0.0');

        // Register your console commands here
        $application->addCommand($container->get(ScanCommand::class));

        return $application;
    },
];
