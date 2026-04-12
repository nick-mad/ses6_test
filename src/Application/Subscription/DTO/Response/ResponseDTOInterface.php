<?php

declare(strict_types=1);

namespace App\Application\Subscription\DTO\Response;

interface ResponseDTOInterface
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(): array;
}
