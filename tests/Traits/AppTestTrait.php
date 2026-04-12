<?php

namespace App\Test\Traits;

use DI\ContainerBuilder;
use Slim\App;

trait AppTestTrait
{
    use ArrayTestTrait;
    use ContainerTestTrait;
    use DatabaseTestTrait;
    use HttpTestTrait;
    use HttpJsonTestTrait;
    use OpenApiTestTrait;

    protected App $app;

    /**
     * Before each test.
     */
    protected function setUp(): void
    {
        $this->setUpApp();
        $this->setUpDatabase();
    }

    protected function setUpApp(): void
    {
        $container = (new ContainerBuilder())
            ->addDefinitions(__DIR__ . '/../../config/container.php')
            ->build();

        $this->app = $container->get(App::class);

        $this->setUpContainer($container);
    }
}
