<?php

declare(strict_types=1);

namespace App\ValueObject\Subscription;

use InvalidArgumentException;

final readonly class SubscribeRequest
{
    public function __construct(
        public string $email,
        public string $repo,
    ) {
        if (!$this->validateEmail($this->email)) {
            throw new InvalidArgumentException('Invalid email format');
        }

        if (!$this->validateRepo($this->repo)) {
            throw new InvalidArgumentException('Invalid repository format');
        }
    }

    private function validateEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    private function validateRepo(string $repo): bool
    {
        return (bool)preg_match('/^[a-zA-Z0-9-]+\/[a-zA-Z0-9-._]+$/', $repo);
    }
}
