<?php

namespace App\Test\Unit\Service\Subscribe;

use App\Application\Subscription\Service\SubscribeParams;
use App\Application\Subscription\Service\SubscribeService;
use App\Domain\Subscription\Client\GithubClientInterface;
use App\Domain\Subscription\Exception\EmailAlreadySubscribedException;
use App\Domain\Subscription\Exception\TokenNotFoundException;
use App\Domain\Subscription\Repository\SubscriptionRepositoryInterface;
use App\Domain\Subscription\Service\NotifierInterface;
use App\Domain\Subscription\ValueObject\Email;
use App\Domain\Subscription\ValueObject\Repo;
use PHPUnit\Framework\TestCase;

class SubscribeServiceTest extends TestCase
{
    private SubscriptionRepositoryInterface $repository;
    private GithubClientInterface $githubClient;
    private NotifierInterface $notifier;
    private SubscribeService $service;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(SubscriptionRepositoryInterface::class);
        $this->githubClient = $this->createMock(GithubClientInterface::class);
        $this->notifier = $this->createMock(NotifierInterface::class);
        $this->service = new SubscribeService($this->repository, $this->githubClient, $this->notifier);
    }

    public function testSubscribeSuccess(): void
    {
        $params = new SubscribeParams(
            new Email('test@example.com'),
            new Repo('owner/repo')
        );

        $this->githubClient->expects($this->once())
            ->method('validateRepository')
            ->with('owner/repo');

        $this->repository->expects($this->once())
            ->method('isSubscribed')
            ->with('test@example.com', 'owner/repo')
            ->willReturn(false);

        $this->repository->expects($this->once())
            ->method('save')
            ->with('test@example.com', 'owner/repo', $this->isType('string'));

        $this->notifier->expects($this->once())
            ->method('sendConfirmation')
            ->with('test@example.com', 'owner/repo', $this->isType('string'));

        $this->service->subscribe($params);
    }

    public function testSubscribeThrowsExceptionIfAlreadySubscribed(): void
    {
        $params = new SubscribeParams(
            new Email('test@example.com'),
            new Repo('owner/repo')
        );

        $this->githubClient->expects($this->once())
            ->method('validateRepository');

        $this->repository->expects($this->once())
            ->method('isSubscribed')
            ->willReturn(true);

        $this->expectException(EmailAlreadySubscribedException::class);

        $this->service->subscribe($params);
    }

    public function testConfirmSuccess(): void
    {
        $token = bin2hex(random_bytes(32));
        $this->repository->expects($this->once())
            ->method('findByToken')
            ->with($token)
            ->willReturn(['id' => 1, 'email' => 'test@example.com']);

        $this->repository->expects($this->once())
            ->method('confirm')
            ->with($token);

        $this->service->confirm($token);
    }

    public function testConfirmThrowsExceptionIfTokenNotFound(): void
    {
        $token = bin2hex(random_bytes(32));
        $this->repository->method('findByToken')->willReturn(null);

        $this->expectException(TokenNotFoundException::class);
        $this->service->confirm($token);
    }

    public function testUnsubscribeSuccess(): void
    {
        $token = bin2hex(random_bytes(32));
        $this->repository->expects($this->once())
            ->method('findByToken')
            ->with($token)
            ->willReturn(['id' => 1, 'email' => 'test@example.com']);

        $this->repository->expects($this->once())
            ->method('unsubscribe')
            ->with($token);

        $this->service->unsubscribe($token);
    }
}
