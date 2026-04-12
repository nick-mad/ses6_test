<?php

declare(strict_types=1);

namespace App\Domain\Subscription\Exception;

use App\Domain\Exception\DomainException;

final class RateLimitExceededException extends DomainException
{
    public function __construct(string $message = 'GitHub API rate limit exceeded. Please try again later.')
    {
        parent::__construct($message, 429);
    }
}
