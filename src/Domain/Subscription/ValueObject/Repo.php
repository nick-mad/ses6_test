<?php

declare(strict_types=1);

namespace App\Domain\Subscription\ValueObject;

use InvalidArgumentException;

final readonly class Repo
{
    public string $value;

    public function __construct(string $value)
    {
        if (!preg_match('/^[a-zA-Z0-9_.-]+\/[a-zA-Z0-9_.-]+$/', $value)) {
            throw new InvalidArgumentException(
                sprintf('Invalid repository format: "%s". Expected "owner/repo"', $value)
            );
        }

        $this->value = $value;
    }
}
