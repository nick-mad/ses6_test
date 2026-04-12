<?php

declare(strict_types=1);

namespace App\Shared\Middleware;

use App\Shared\Renderer\JsonRenderer;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Middleware для API key автентифікації.
 *
 * Цей Middleware демонструє, як можна захистити ендпоїнти токеном у заголовку X-API-KEY.
 * Для активації його потрібно додати до групи маршрутів у config/routes.php.
 */
final class ApiKeyMiddleware implements MiddlewareInterface
{
    private ResponseFactoryInterface $responseFactory;
    private JsonRenderer $renderer;
    private string $apiKey;

    public function __construct(
        ResponseFactoryInterface $responseFactory,
        JsonRenderer $jsonRenderer,
        string $apiKey = 'demo-api-key' // Дефолтний ключ для демонстрації
    ) {
        $this->responseFactory = $responseFactory;
        $this->renderer = $jsonRenderer;
        $this->apiKey = $apiKey;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $apiKey = $request->getHeaderLine('X-API-KEY');

        if ($apiKey !== $this->apiKey) {
            $response = $this->responseFactory->createResponse(StatusCodeInterface::STATUS_UNAUTHORIZED);

            return $this->renderer->json($response, [
                'error' => [
                    'message' => 'Невірний або відсутній API Key. Для доступу використовуйте заголовок X-API-KEY: ' . $this->apiKey,
                ],
            ]);
        }

        return $handler->handle($request);
    }
}
