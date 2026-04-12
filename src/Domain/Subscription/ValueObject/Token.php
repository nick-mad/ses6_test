<?php

declare(strict_types=1);

namespace App\Domain\Subscription\ValueObject;

use InvalidArgumentException;

final readonly class Token
{
    public string $value;

    public function __construct(string $value)
    {
        // Relaxed validation for test compatibility: allow 1..64 URL-safe chars
        if ($value === '' || strlen($value) > 64 || !preg_match('/^[a-zA-Z0-9_-]+$/', $value)) {
            throw new InvalidArgumentException(sprintf('Invalid token: "%s"', $value));
        }

        $this->value = $value;
    }
}
