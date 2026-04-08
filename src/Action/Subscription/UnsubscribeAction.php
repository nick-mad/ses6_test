<?php

declare(strict_types=1);

namespace App\Action\Subscription;

use App\Renderer\JsonRenderer;
use App\ValueObject\Subscription\TokenRequest;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final readonly class UnsubscribeAction
{
    public function __construct(private JsonRenderer $renderer)
    {
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args,
    ): ResponseInterface {
        try {
            $tokenRequest = new TokenRequest((string)($args['token'] ?? ''));
        } catch (InvalidArgumentException) {
            return $this->renderer->json($response->withStatus(400), [
                'error' => 'Invalid token',
            ]);
        }

        return $this->renderer->json($response, ['success' => true]);
    }
}
