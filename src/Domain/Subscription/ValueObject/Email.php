<?php

declare(strict_types=1);

namespace App\Domain\Subscription\ValueObject;

use InvalidArgumentException;

final readonly class Email
{
    public string $value;

    public function __construct(string $value)
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException(sprintf('Invalid email address: "%s"', $value));
        }

        $this->value = $value;
    }
}
