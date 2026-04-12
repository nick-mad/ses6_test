<?php

declare(strict_types=1);

namespace App\Presentation\Action\Subscription;

use App\Application\Subscription\DTO\Request\SubscribeDTO;
use App\Domain\Subscription\Provider\SubscriptionProviderInterface;
use App\Shared\Renderer\JsonRenderer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final readonly class GetTokenAction
{
    public function __construct(
        private JsonRenderer $renderer,
        private SubscriptionProviderInterface $provider
    ) {
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        $subscribeDTO = SubscribeDTO::fromRequest($request);

        $token = $this->provider->getToken($subscribeDTO);

        return $this->renderer->json($response, [
            'success' => true,
            'token' => $token,
        ]);
    }
}
