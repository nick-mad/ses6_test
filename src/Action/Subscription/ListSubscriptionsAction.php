<?php

declare(strict_types=1);

namespace App\Action\Subscription;

use App\Renderer\JsonRenderer;
use App\ValueObject\Subscription\ListSubscriptionsRequest;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final readonly class ListSubscriptionsAction
{
    public function __construct(private JsonRenderer $renderer)
    {
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = $request->getQueryParams();

        try {
            $listRequest = new ListSubscriptionsRequest((string)($params['email'] ?? ''));
        } catch (InvalidArgumentException) {
            return $this->renderer->json($response->withStatus(400), [
                'error' => 'Invalid email',
            ]);
        }

        return $this->renderer->json($response, ['success' => true]);
    }
}
