<?php

declare(strict_types=1);

namespace App\Presentation\Action\Subscription;

use App\Application\Subscription\DTO\Request\ListSubscriptionsDTO;
use App\Domain\Subscription\Provider\SubscriptionProviderInterface;
use App\Shared\Renderer\JsonRenderer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final readonly class ListSubscriptionsAction
{
    public function __construct(
        private JsonRenderer $renderer,
        private SubscriptionProviderInterface $provider
    ) {
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $dto = ListSubscriptionsDTO::fromRequest($request);

        $subscriptions = $this->provider->getSubscriptions($dto);

        return $this->renderer->json(
            $response,
            array_map(
                static fn($subscription) => $subscription->toArray(),
                $subscriptions
            )
        );
    }
}
