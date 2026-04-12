<?php

use App\Application\Handlers\HttpErrorHandler;
use App\Application\Handlers\ShutdownHandler;
use App\Application\ResponseEmitter\ResponseEmitter;
use Nyholm\Psr7\Factory\Psr17Factory;
use Slim\App;
use Slim\Factory\ServerRequestCreatorFactory;
use Spiral\RoadRunner\Http\PSR7Worker;
use Spiral\RoadRunner\Worker;

require __DIR__ . '/vendor/autoload.php';

/** @var App $app */
$app = require __DIR__ . '/config/bootstrap.php';

$worker = Worker::create();
$psrFactory = new Psr17Factory();
$psr7Worker = new PSR7Worker($worker, $psrFactory, $psrFactory, $psrFactory);

while ($request = $psr7Worker->waitRequest()) {
    try {
        $response = $app->handle($request);
        $psr7Worker->respond($response);
    } catch (\Throwable $e) {
        $psr7Worker->getWorker()->error((string)$e);
    }
}
