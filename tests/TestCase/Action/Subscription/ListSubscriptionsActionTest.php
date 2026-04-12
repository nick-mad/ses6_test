<?php

namespace App\Test\TestCase\Action\Subscription;

use App\Presentation\Action\Subscription\ListSubscriptionsAction;
use App\Test\Traits\AppTestTrait;
use Fig\Http\Message\StatusCodeInterface;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[UsesClass(ListSubscriptionsAction::class)]
class ListSubscriptionsActionTest extends TestCase
{
    use AppTestTrait;

    public function testListSuccess(): void
    {
        $request = $this->createRequest('GET', '/api/subscriptions?email=test@example.com');
        $response = $this->app->handle($request);

        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    public function testListInvalidEmail(): void
    {
        $request = $this->createRequest('GET', '/api/subscriptions?email=invalid-email');
        $response = $this->app->handle($request);

        $this->assertSame(StatusCodeInterface::STATUS_BAD_REQUEST, $response->getStatusCode());
    }

    public function testListMissingEmail(): void
    {
        $request = $this->createRequest('GET', '/api/subscriptions');
        $response = $this->app->handle($request);

        $this->assertSame(StatusCodeInterface::STATUS_BAD_REQUEST, $response->getStatusCode());
    }
}
