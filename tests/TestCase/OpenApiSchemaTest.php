<?php

namespace App\Test\TestCase;

use App\Test\Traits\AppTestTrait;
use PHPUnit\Framework\TestCase;

class OpenApiSchemaTest extends TestCase
{
    use AppTestTrait;

    public function testConfirmSchema(): void
    {
        $request = $this->createRequest('GET', '/api/confirm/test-token');
        $response = $this->app->handle($request);

        $this->validateResponse($response, '/confirm/{token}', 'get');
        $this->assertTrue(true);
    }

    public function testUnsubscribeSchema(): void
    {
        $request = $this->createRequest('GET', '/api/unsubscribe/test-token');
        $response = $this->app->handle($request);

        $this->validateResponse($response, '/unsubscribe/{token}', 'get');
        $this->assertTrue(true);
    }

    public function testSubscribeSchema(): void
    {
        $data = [
            'email' => 'test@example.com',
            'repo' => 'odan/slim4-skeleton',
        ];
        // Ensure we request JSON for the error response (409 conflict)
        $request = $this->createFormRequest('POST', '/api/subscribe', $data)
            ->withHeader('Accept', 'application/json');
        $response = $this->app->handle($request);

        $this->validateResponse($response, '/subscribe', 'post');
        $this->assertTrue(true);
    }

    public function testListSubscriptionsSchema(): void
    {
        $request = $this->createRequest('GET', '/api/subscriptions')
            ->withQueryParams(['email' => 'test@example.com']);
        $response = $this->app->handle($request);

        $this->validateResponse($response, '/subscriptions', 'get');
        $this->assertTrue(true);
    }
}
