<?php

namespace App\Test\TestCase\Action\Subscription;

use App\Presentation\Action\Subscription\ConfirmSubscriptionAction;
use App\Test\Traits\AppTestTrait;
use Fig\Http\Message\StatusCodeInterface;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[UsesClass(ConfirmSubscriptionAction::class)]
class ConfirmSubscriptionActionTest extends TestCase
{
    use AppTestTrait;

    public function testConfirmSuccess(): void
    {
        // 'other-token' exists in SubscriptionSeeder and is unconfirmed (confirmed=0)
        $request = $this->createRequest('GET', '/api/confirm/other-token');
        $response = $this->app->handle($request);

        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    // В данном случае token в пути обязателен по маршруту /api/confirm/{token}
    // Если его нет, Slim вернет 404 (маршрут не найден) или 405.
    // Но мы можем проверить пустой токен, если маршрут позволит или если мы тестируем VO.
}
