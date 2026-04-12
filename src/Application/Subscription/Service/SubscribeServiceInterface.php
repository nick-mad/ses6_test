<?php

declare(strict_types=1);

namespace App\Application\Subscription\Service;

interface SubscribeServiceInterface
{
    public function subscribe(SubscribeParams $params): void;

    public function confirm(string $token): void;

    public function unsubscribe(string $token): void;
}
