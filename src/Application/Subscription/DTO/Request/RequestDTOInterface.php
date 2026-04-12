<?php

declare(strict_types=1);

namespace App\Application\Subscription\DTO\Request;

use Psr\Http\Message\ServerRequestInterface;

interface RequestDTOInterface
{
    /**
     * @param array<string, mixed> $args
     * @param ServerRequestInterface $request
     */
    public static function fromRequest(ServerRequestInterface $request, array $args = []): self;
}
