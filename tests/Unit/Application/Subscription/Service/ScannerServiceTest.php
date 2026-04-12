<?php

namespace Tests\Unit\Application\Subscription\Service;

use App\Application\Subscription\Service\ScannerService;
use App\Domain\Subscription\Client\GithubClientInterface;
use App\Domain\Subscription\Exception\RateLimitExceededException;
use App\Domain\Subscription\Repository\SubscriptionRepositoryInterface;
use App\Domain\Subscription\Service\NotifierInterface;
use Exception;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class ScannerServiceTest extends TestCase
{
    private $repository;
    private $githubClient;
    private $notifier;
    private $logger;
    private $service;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(SubscriptionRepositoryInterface::class);
        $this->githubClient = $this->createMock(GithubClientInterface::class);
        $this->notifier = $this->createMock(NotifierInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->service = new ScannerService(
            $this->repository,
            $this->githubClient,
            $this->notifier,
            $this->logger
        );
    }

    public function testScanSendsNotificationOnNewTag(): void
    {
        $subscriptions = [
            [
                'id' => 1,
                'email' => 'user@example.com',
                'repo' => 'owner/repo',
                'last_seen_tag' => 'v1.0.0',
            ],
        ];

        $this->repository->method('findAllActive')->willReturn($subscriptions);
        $this->githubClient->method('getLatestTag')->with('owner/repo')->willReturn('v1.1.0');

        $this->notifier->expects($this->once())
            ->method('notify')
            ->with('user@example.com', 'owner/repo', 'v1.1.0');

        $this->repository->expects($this->once())
            ->method('updateLastSeenTag')
            ->with(1, 'v1.1.0');

        $this->service->scan();
    }

    public function testScanDoesNotSendNotificationIfTagIsSame(): void
    {
        $subscriptions = [
            [
                'id' => 1,
                'email' => 'user@example.com',
                'repo' => 'owner/repo',
                'last_seen_tag' => 'v1.1.0',
            ],
        ];

        $this->repository->method('findAllActive')->willReturn($subscriptions);
        $this->githubClient->method('getLatestTag')->with('owner/repo')->willReturn('v1.1.0');

        $this->notifier->expects($this->never())->method('notify');
        $this->repository->expects($this->never())->method('updateLastSeenTag');

        $this->service->scan();
    }

    public function testScanSendsNotificationOnFirstTag(): void
    {
        $subscriptions = [
            [
                'id' => 1,
                'email' => 'user@example.com',
                'repo' => 'owner/repo',
                'last_seen_tag' => null,
            ],
        ];

        $this->repository->method('findAllActive')->willReturn($subscriptions);
        $this->githubClient->method('getLatestTag')->with('owner/repo')->willReturn('v1.0.0');

        $this->notifier->expects($this->once())
            ->method('notify')
            ->with('user@example.com', 'owner/repo', 'v1.0.0');

        $this->repository->expects($this->once())
            ->method('updateLastSeenTag')
            ->with(1, 'v1.0.0');

        $this->service->scan();
    }

    public function testScanHandlesNullLatestTag(): void
    {
        $subscriptions = [
            [
                'id' => 1,
                'email' => 'user@example.com',
                'repo' => 'owner/repo',
                'last_seen_tag' => null,
            ],
        ];

        $this->repository->method('findAllActive')->willReturn($subscriptions);
        $this->githubClient->method('getLatestTag')->with('owner/repo')->willReturn(null);

        $this->logger->expects($this->once())
            ->method('debug')
            ->with($this->stringContains('No tags found'));

        $this->notifier->expects($this->never())->method('notify');

        $this->service->scan();
    }

    public function testScanHandlesGenericException(): void
    {
        $subscriptions = [
            [
                'id' => 1,
                'email' => 'user@example.com',
                'repo' => 'owner/repo',
                'last_seen_tag' => null,
            ],
        ];

        $this->repository->method('findAllActive')->willReturn($subscriptions);
        $this->githubClient->method('getLatestTag')->willThrowException(new Exception('Some Error'));

        $this->logger->expects($this->once())
            ->method('error')
            ->with($this->stringContains('Error scanning repo'));

        $this->service->scan();
    }
}
