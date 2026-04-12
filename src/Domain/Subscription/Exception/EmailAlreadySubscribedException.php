<?php

declare(strict_types=1);

namespace App\Domain\Subscription\Exception;

use App\Domain\Exception\DomainException;

final class EmailAlreadySubscribedException extends DomainException
{
    public function __construct(string $message = 'Email already subscribed to this repository', int $code = 409)
    {
        parent::__construct($message, $code);
    }
}
