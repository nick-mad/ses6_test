<?php

declare(strict_types=1);

namespace App\Presentation\Action\Subscription;

use App\Application\Subscription\DTO\Request\SubscribeDTO;
use App\Application\Subscription\Service\SubscribeParams;
use App\Application\Subscription\Service\SubscribeServiceInterface;
use App\Shared\Renderer\JsonRenderer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final readonly class SubscribeAction
{
    public function __construct(
        private JsonRenderer $renderer,
        private SubscribeServiceInterface $service
    ) {
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        $subscribeDTO = SubscribeDTO::fromRequest($request);

        $this->service->subscribe(
            SubscribeParams::formDTO($subscribeDTO)
        );

        return $this->renderer->json($response, [
            'success' => true,
            'message' => 'Subscription successful',
        ]);
    }
}
