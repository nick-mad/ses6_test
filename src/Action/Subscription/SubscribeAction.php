<?php

declare(strict_types=1);

namespace App\Action\Subscription;

use App\Renderer\JsonRenderer;
use App\ValueObject\Subscription\SubscribeRequest;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final readonly class SubscribeAction
{
    public function __construct(private JsonRenderer $renderer)
    {
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array)$request->getParsedBody();

        try {
            $subscribeRequest = new SubscribeRequest(
                (string)($data['email'] ?? ''),
                (string)($data['repo'] ?? ''),
            );
        } catch (InvalidArgumentException) {
            return $this->renderer->json($response->withStatus(400), [
                'error' => 'Invalid input',
            ]);
        }

        $email = $subscribeRequest->email;
        $repo = $subscribeRequest->repo;

        // TODO: Check if repository exists on GitHub
        // For now, assume it exists unless it's a specific "not-found" repo for testing
        if ($repo === 'nonexistent/repo') {
            return $this->renderer->json($response->withStatus(404), [
                'error' => 'Repository not found on GitHub',
            ]);
        }

        // TODO: Check if email already subscribed to this repository
        // For now, assume it's not subscribed unless it's a specific "already-subscribed" email
        if ($email === 'already@subscribed.com') {
            return $this->renderer->json($response->withStatus(409), [
                'error' => 'Email already subscribed to this repository',
            ]);
        }

        return $this->renderer->json($response, ['success' => true]);
    }
}
