<?php

namespace App\Test\TestCase\Infrastructure\Persistence;

use App\Infrastructure\Persistence\PdoSubscriptionRepository;
use App\Test\Traits\AppTestTrait;
use PDO;
use PHPUnit\Framework\TestCase;

class PdoSubscriptionRepositoryTest extends TestCase
{
    use AppTestTrait;

    private PdoSubscriptionRepository $repository;
    private PDO $pdo;

    protected function setUp(): void
    {
        $this->setUpApp();
        $this->setUpDatabase(); // This also runs seeds
        $this->pdo = $this->container->get(PDO::class);
        $this->repository = new PdoSubscriptionRepository($this->pdo);
    }

    public function testSave(): void
    {
        $this->repository->save('new@example.com', 'owner/repo', 'new-token', 'tag');

        $sql = 'SELECT COUNT(*) FROM subscriptions WHERE email = :email AND repo = :repo';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['email' => 'new@example.com', 'repo' => 'owner/repo']);
        $this->assertEquals(1, $stmt->fetchColumn());
    }

    public function testIsSubscribed(): void
    {
        // 'test@example.com' comes from SubscriptionSeeder
        $this->assertTrue($this->repository->isSubscribed('test@example.com', 'odan/slim4-skeleton'));
        $this->assertFalse($this->repository->isSubscribed('not@subscribed.com', 'owner/repo'));
    }

    public function testFindByToken(): void
    {
        $subscription = $this->repository->findByToken('test-token');
        $this->assertNotNull($subscription);
        $this->assertEquals('test@example.com', $subscription['email']);

        $this->assertNull($this->repository->findByToken('non-existent-token'));
    }

    public function testConfirm(): void
    {
        $this->repository->confirm('other-token');

        $subscription = $this->repository->findByToken('other-token');
        $this->assertEquals(1, $subscription['confirmed']);
    }

    public function testUnsubscribe(): void
    {
        $this->repository->unsubscribe('test-token');

        $subscription = $this->repository->findByToken('test-token');
        $this->assertNotNull($subscription['unsubscribed_at']);
    }

    public function testUpdateLastSeenTag(): void
    {
        $this->repository->updateLastSeenTag(1, 'v2.0.0');

        $stmt = $this->pdo->prepare('SELECT last_seen_tag FROM subscriptions WHERE id = 1');
        $stmt->execute();
        $this->assertEquals('v2.0.0', $stmt->fetchColumn());
    }

    public function testFindActiveByEmail(): void
    {
        $subscriptions = $this->repository->findActiveByEmail('test@example.com');
        $this->assertCount(1, $subscriptions);
        $this->assertEquals('odan/slim4-skeleton', $subscriptions[0]['repo']);
    }

    public function testFindAllActive(): void
    {
        $subscriptions = $this->repository->findAllActive();
        $this->assertGreaterThanOrEqual(1, count($subscriptions));
    }
}
