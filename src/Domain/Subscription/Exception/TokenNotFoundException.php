<?php

declare(strict_types=1);

namespace App\Domain\Subscription\Exception;

use App\Domain\Exception\DomainException;

final class TokenNotFoundException extends DomainException
{
    public function __construct(string $message = 'Token not found', int $code = 404)
    {
        parent::__construct($message, $code);
    }
}
