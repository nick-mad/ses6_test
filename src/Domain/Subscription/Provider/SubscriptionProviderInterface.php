<?php

declare(strict_types=1);

namespace App\Domain\Subscription\Provider;

use App\Application\Subscription\DTO\Request\ListSubscriptionsDTO as RequestDTO;
use App\Application\Subscription\DTO\Request\SubscribeDTO;
use App\Application\Subscription\DTO\Response\ListSubscriptionsDTO as ResponseDTO;
use App\Application\Subscription\Service\SubscribeParams;

interface SubscriptionProviderInterface
{
    /**
     * @param RequestDTO $params
     * @return ResponseDTO[]
     */
    public function getSubscriptions(RequestDTO $params): array;

    public function getToken(SubscribeDTO $params): ?string;
}
