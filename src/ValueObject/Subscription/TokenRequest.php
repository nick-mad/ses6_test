<?php

declare(strict_types=1);

namespace App\ValueObject\Subscription;

use InvalidArgumentException;

final readonly class TokenRequest
{
    public function __construct(
        public string $token,
    ) {
        if (empty($this->token)) {
            throw new InvalidArgumentException('Token cannot be empty');
        }
    }
}
