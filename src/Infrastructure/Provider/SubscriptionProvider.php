<?php

declare(strict_types=1);

namespace App\Infrastructure\Provider;

use App\Application\Subscription\DTO\Request\ListSubscriptionsDTO as RequestDTO;
use App\Application\Subscription\DTO\Request\SubscribeDTO;
use App\Application\Subscription\DTO\Response\ListSubscriptionsDTO as ResponseDTO;
use App\Application\Subscription\Service\SubscribeParams;
use App\Domain\Subscription\Provider\SubscriptionProviderInterface;
use App\Domain\Subscription\Repository\SubscriptionRepositoryInterface;

final readonly class SubscriptionProvider implements SubscriptionProviderInterface
{
    public function __construct(private SubscriptionRepositoryInterface $repository)
    {
    }

    public function getSubscriptions(RequestDTO $params): array
    {
        $rows = $this->repository->findActiveByEmail($params->email->value);

        return array_map(static fn(array $row) => ResponseDTO::fromArray($row), $rows);
    }

    public function getToken(SubscribeDTO $params): ?string
    {
        return $this->repository->findTokenByEmailAndRepo($params->email->value, $params->repo->value);
    }
}
