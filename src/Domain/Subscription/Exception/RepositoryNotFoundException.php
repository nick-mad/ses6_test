<?php

declare(strict_types=1);

namespace App\Domain\Subscription\Exception;

use App\Domain\Exception\DomainException;

final class RepositoryNotFoundException extends DomainException
{
    public function __construct(string $message = 'Repository not found on GitHub')
    {
        parent::__construct($message, 404);
    }
}
