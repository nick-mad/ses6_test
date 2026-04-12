<?php

declare(strict_types=1);

namespace App\Presentation\Action\Subscription;

use App\Application\Subscription\DTO\Request\TokenDTO;
use App\Application\Subscription\Service\SubscribeServiceInterface;
use App\Shared\Renderer\JsonRenderer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final readonly class ConfirmSubscriptionAction
{
    public function __construct(
        private JsonRenderer $renderer,
        private SubscribeServiceInterface $service
    ) {
    }

    /**
     * @param array<string, mixed> $args
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     */
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args,
    ): ResponseInterface {
        $dto = TokenDTO::fromRequest($request, $args);

        $this->service->confirm($dto->token->value);

        return $this->renderer->json($response, [
            'success' => true,
            'message' => 'Subscription confirmed successfully',
        ]);
    }
}
