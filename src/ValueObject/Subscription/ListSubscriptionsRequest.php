<?php

declare(strict_types=1);

namespace App\ValueObject\Subscription;

use InvalidArgumentException;

final readonly class ListSubscriptionsRequest
{
    public function __construct(
        public string $email,
    ) {
        if (!$this->validateEmail($this->email)) {
            throw new InvalidArgumentException('Invalid email format');
        }
    }

    private function validateEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
}
