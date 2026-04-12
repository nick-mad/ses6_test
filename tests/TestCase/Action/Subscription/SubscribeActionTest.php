<?php

namespace App\Test\TestCase\Action\Subscription;

use App\Domain\Subscription\Client\GithubClientInterface;
use App\Domain\Subscription\Exception\RepositoryNotFoundException;
use App\Presentation\Action\Subscription\SubscribeAction;
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
            'email' => 'new@example.com',
            'repo' => 'owner/repo',
        ]);

        // Mock the github client in the container
        $githubClient = $this->createMock(GithubClientInterface::class);
        $githubClient->expects($this->once())->method('validateRepository');
        $this->container->set(GithubClientInterface::class, $githubClient);

        $response = $this->app->handle($request);

        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    public function testSubscribeInvalidEmail(): void
    {
        $request = $this->createFormRequest('POST', '/api/subscribe', [
            'email' => 'invalid-email',
            'repo' => 'owner/repo',
        ]);
        $response = $this->app->handle($request);

        $this->assertSame(StatusCodeInterface::STATUS_BAD_REQUEST, $response->getStatusCode());
    }

    public function testSubscribeInvalidRepo(): void
    {
        $request = $this->createFormRequest('POST', '/api/subscribe', [
            'email' => 'test@example.com',
            'repo' => 'invalid-repo-format',
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
        $githubClient = $this->createMock(GithubClientInterface::class);
        $githubClient->expects($this->once())->method('validateRepository');
        $this->container->set(GithubClientInterface::class, $githubClient);

        $request = $this->createRequest('POST', '/api/subscribe')
            ->withHeader('Content-Type', 'application/json');
        $request->getBody()->write(
            (string)json_encode([
                'email' => 'json@example.com',
                'repo' => 'owner/repo',
            ])
        );
        // BodyParsingMiddleware requires the body to be at the beginning or manually set parsed body
        $request = $request->withParsedBody([
            'email' => 'json@example.com',
            'repo' => 'owner/repo',
        ]);

        $response = $this->app->handle($request);

        $this->assertSame(StatusCodeInterface::STATUS_OK, $response->getStatusCode());
    }

    public function testSubscribeRepoNotFound(): void
    {
        $githubClient = $this->createMock(GithubClientInterface::class);
        $githubClient->method('validateRepository')->willThrowException(new RepositoryNotFoundException());
        $this->container->set(GithubClientInterface::class, $githubClient);

        $request = $this->createFormRequest('POST', '/api/subscribe', [
            'email' => 'test@example.com',
            'repo' => 'nonexistent/repo',
        ]);
        $response = $this->app->handle($request);

        $this->assertSame(StatusCodeInterface::STATUS_NOT_FOUND, $response->getStatusCode());
    }

    public function testSubscribeAlreadyExists(): void
    {
        // 'test@example.com' with 'odan/slim4-skeleton' already exists in seeds
        $request = $this->createFormRequest('POST', '/api/subscribe', [
            'email' => 'test@example.com',
            'repo' => 'odan/slim4-skeleton',
        ]);

        $githubClient = $this->createMock(GithubClientInterface::class);
        $this->container->set(GithubClientInterface::class, $githubClient);

        $response = $this->app->handle($request);

        $this->assertSame(StatusCodeInterface::STATUS_CONFLICT, $response->getStatusCode());
    }
}
