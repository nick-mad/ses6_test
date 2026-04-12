<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Subscription\Repository\SubscriptionRepositoryInterface;
use PDO;

final readonly class PdoSubscriptionRepository implements SubscriptionRepositoryInterface
{
    public function __construct(private PDO $pdo)
    {
    }

    public function save(string $email, string $repo, string $token, ?string $tag): void
    {
        $sql = 'INSERT INTO subscriptions (email, repo, token, last_seen_tag)
                VALUES (:email, :repo, :token, :tag)';

        $statement = $this->pdo->prepare($sql);
        $statement->execute(['email' => $email, 'repo' => $repo, 'token' => $token, 'tag' => $tag]);
    }

    public function isSubscribed(string $email, string $repo): bool
    {
        $sql = 'SELECT COUNT(*)
                FROM subscriptions
                WHERE email = :email AND repo = :repo AND unsubscribed_at IS NULL';

        $statement = $this->pdo->prepare($sql);
        $statement->execute(['email' => $email, 'repo' => $repo]);
        return (bool)$statement->fetchColumn();
    }

    public function findByToken(string $token): ?array
    {
        $sql = 'SELECT * FROM subscriptions WHERE token = :token';

        $statement = $this->pdo->prepare($sql);
        $statement->execute(['token' => $token]);
        /** @var array<string, mixed>|false $row */
        $row = $statement->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function confirm(string $token): void
    {
        $sql = 'UPDATE subscriptions SET confirmed = TRUE WHERE token = :token';

        $statement = $this->pdo->prepare($sql);
        $statement->execute(['token' => $token]);
    }

    public function unsubscribe(string $token): void
    {
        $sql = 'UPDATE subscriptions SET unsubscribed_at = CURRENT_TIMESTAMP WHERE token = :token';

        $statement = $this->pdo->prepare($sql);
        $statement->execute(['token' => $token]);
    }

    public function updateLastSeenTag(int $id, string $tag): void
    {
        $sql = 'UPDATE subscriptions SET last_seen_tag = :tag WHERE id = :id';

        $statement = $this->pdo->prepare($sql);
        $statement->execute(['tag' => $tag, 'id' => $id]);
    }

    public function updateLastScannedAt(int $id): void
    {
        $sql = 'UPDATE subscriptions SET last_scanned_at = CURRENT_TIMESTAMP WHERE id = :id';

        $statement = $this->pdo->prepare($sql);
        $statement->execute(['id' => $id]);
    }

    public function findActiveByEmail(string $email): array
    {
        $sql = 'SELECT email, repo, confirmed, last_seen_tag
                FROM subscriptions
                WHERE email = :email AND confirmed = TRUE AND unsubscribed_at IS NULL';

        $statement = $this->pdo->prepare($sql);
        $statement->execute(['email' => $email]);
        /** @var array<int, array<string, mixed>> $rows */
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $rows;
    }

    public function findAllActive(): array
    {
        $sql = 'SELECT * FROM subscriptions
                WHERE confirmed = TRUE AND unsubscribed_at IS NULL
                ORDER BY last_scanned_at ASC NULLS FIRST, id ASC';

        $statement = $this->pdo->query($sql);
        if ($statement === false) {
            return [];
        }
        /** @var array<int, array<string, mixed>> $rows */
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $rows;
    }
}
