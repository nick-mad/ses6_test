<?php

namespace App\Test\TestCase\Action\Subscription;

use App\Action\Subscription\SubscribeAction;
use App\Test\Traits\AppTestTrait;
use Fig\Http\Message\StatusCodeInterface;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[UsesClass(SubscribeAction::class)]
class SubscribeActionTest extends TestCase
{
    use AppTestTrait;

    public function testSubscribeSuccess(): void
    {
        $request = $this->createFormRequest('POST', '/api/subscribe', [
            'email' => 'test@example.com',
            'repo' => 'owner/repo'
        ]);
        $response = $this->app->handle($request);

        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    public function testSubscribeInvalidEmail(): void
    {
        $request = $this->createFormRequest('POST', '/api/subscribe', [
            'email' => 'invalid-email',
            'repo' => 'owner/repo'
        ]);
        $response = $this->app->handle($request);

        $this->assertSame(StatusCodeInterface::STATUS_BAD_REQUEST, $response->getStatusCode());
    }

    public function testSubscribeInvalidRepo(): void
    {
        $request = $this->createFormRequest('POST', '/api/subscribe', [
            'email' => 'test@example.com',
            'repo' => 'invalid-repo-format'
        ]);
        $response = $this->app->handle($request);

        $this->assertSame(StatusCodeInterface::STATUS_BAD_REQUEST, $response->getStatusCode());
    }

    public function testSubscribeMissingParameters(): void
    {
        $request = $this->createFormRequest('POST', '/api/subscribe', []);
        $response = $this->app->handle($request);

        $this->assertSame(StatusCodeInterface::STATUS_BAD_REQUEST, $response->getStatusCode());
    }

    public function testSubscribeJsonRequest(): void
    {
        $request = $this->createRequest('POST', '/api/subscribe')
            ->withHeader('Content-Type', 'application/json');
        $request->getBody()->write((string)json_encode([
            'email' => 'test@example.com',
            'repo' => 'owner/repo'
        ]));
        
        $response = $this->app->handle($request);

        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    public function testSubscribeRepoNotFound(): void
    {
        $request = $this->createFormRequest('POST', '/api/subscribe', [
            'email' => 'test@example.com',
            'repo' => 'nonexistent/repo'
        ]);
        $response = $this->app->handle($request);

        $this->assertSame(StatusCodeInterface::STATUS_NOT_FOUND, $response->getStatusCode());
    }

    public function testSubscribeAlreadyExists(): void
    {
        $request = $this->createFormRequest('POST', '/api/subscribe', [
            'email' => 'already@subscribed.com',
            'repo' => 'owner/repo'
        ]);
        $response = $this->app->handle($request);

        $this->assertSame(StatusCodeInterface::STATUS_CONFLICT, $response->getStatusCode());
    }
}
