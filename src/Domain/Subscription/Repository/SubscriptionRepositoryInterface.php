<?php

declare(strict_types=1);

namespace App\Domain\Subscription\Repository;

interface SubscriptionRepositoryInterface
{
    public function save(string $email, string $repo, string $token, ?string $tag): void;

    public function isSubscribed(string $email, string $repo): bool;

    /**
     * @param string $token
     * @return array<string, mixed>|null
     */
    public function findByToken(string $token): ?array;

    public function confirm(string $token): void;

    public function unsubscribe(string $token): void;

    public function updateLastSeenTag(int $id, string $tag): void;

    public function updateLastScannedAt(int $id): void;

    /** @return array<int, array<string, mixed>> */
    public function findActiveByEmail(string $email): array;

    /** @return array<int, array<string, mixed>> */
    public function findAllActive(): array;
}
