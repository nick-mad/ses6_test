<?php

declare(strict_types=1);

namespace App\Presentation\Action\Home;

use App\Shared\Renderer\TemplateRenderer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class HomeAction
{
    public function __construct(private readonly TemplateRenderer $renderer)
    {
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->renderer->render($response, 'subscribe.php');
    }
}
