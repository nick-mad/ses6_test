<?php

// Define app routes

use App\Action\Home;
use App\Action\Subscription;
use Slim\App;

return function (App $app) {
    $app->get('/', Home\HomeAction::class)->setName('home');
    $app->get('/ping', Home\PingAction::class);

    $app->group('/api', function (\Slim\Routing\RouteCollectorProxy $group) {
        $group->post('/subscribe', Subscription\SubscribeAction::class);
        $group->get('/confirm/{token}', Subscription\ConfirmSubscriptionAction::class);
        $group->get('/unsubscribe/{token}', Subscription\UnsubscribeAction::class);
        $group->get('/subscriptions', Subscription\ListSubscriptionsAction::class);
    });
};
