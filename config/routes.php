<?php

use App\Presentation\Action\Health;
use App\Presentation\Action\Home;
use App\Presentation\Action\Subscription;
use App\Shared\Middleware\ApiKeyMiddleware;
use Slim\App;

return function (App $app) {
    $app->get('/', Home\HomeAction::class)->setName('home');
    $app->get('/health', Health\HealthAction::class);

    $app->group('/api', function (Slim\Routing\RouteCollectorProxy $group) {
        $group->post('/subscribe', Subscription\SubscribeAction::class);
        $group->post('/subscription/token', Subscription\GetTokenAction::class);
        $group->get('/confirm/{token}', Subscription\ConfirmSubscriptionAction::class);
        $group->get('/unsubscribe/{token}', Subscription\UnsubscribeAction::class);
        $group->get('/subscriptions', Subscription\ListSubscriptionsAction::class);
    }); //->add(ApiKeyMiddleware::class); // Для активації API Key автентифікації розкоментуйте цей рядок
};
