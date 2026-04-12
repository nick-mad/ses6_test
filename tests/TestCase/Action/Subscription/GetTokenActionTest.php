<?php

namespace App\Test\TestCase\Action\Subscription;

use App\Presentation\Action\Subscription\GetTokenAction;
use App\Test\Traits\AppTestTrait;
use Fig\Http\Message\StatusCodeInterface;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[UsesClass(GetTokenAction::class)]
class GetTokenActionTest extends TestCase
{
    use AppTestTrait;

    public function testGetTokenSuccess(): void
    {
        // Use existing subscription from seeds
        $email = 'test@example.com';
        $repo = 'odan/slim4-skeleton';

        $request = $this->createRequest('POST', '/api/subscription/token')
            ->withHeader('Content-Type', 'application/json')
            ->withParsedBody([
                'email' => $email,
                'repo' => $repo,
            ]);

        $response = $this->app->handle($request);

        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());

        $data = json_decode((string)$response->getBody(), true);
        $this->assertTrue($data['success']);
        $this->assertNotNull($data['token']);
    }

    public function testGetTokenNotFound(): void
    {
        $request = $this->createRequest('POST', '/api/subscription/token')
            ->withHeader('Content-Type', 'application/json')
            ->withParsedBody([
                'email' => 'nonexistent@example.com',
                'repo' => 'nonexistent/repo',
            ]);

        $response = $this->app->handle($request);

        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());

        $data = json_decode((string)$response->getBody(), true);
        $this->assertTrue($data['success']);
        $this->assertNull($data['token']);
    }

    public function testGetTokenMissingParams(): void
    {
        $request = $this->createRequest('POST', '/api/subscription/token');

        $response = $this->app->handle($request);

        $this->assertSame(StatusCodeInterface::STATUS_BAD_REQUEST, $response->getStatusCode());
    }
}
