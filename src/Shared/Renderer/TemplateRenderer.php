<?php

declare(strict_types=1);

namespace App\Shared\Renderer;

use Psr\Http\Message\ResponseInterface;

final readonly class TemplateRenderer
{
    public function __construct(private string $templatesPath)
    {
    }

    /**
     * @param array<string, mixed> $params
     * @param ResponseInterface $response
     * @param string $template
     */
    public function render(ResponseInterface $response, string $template, array $params = []): ResponseInterface
    {
        $file = rtrim($this->templatesPath, '/') . '/' . ltrim($template, '/');
        if (!is_file($file)) {
            $response->getBody()->write(sprintf('Template not found: %s', $template));
            return $response->withStatus(500);
        }

        $content = (string)file_get_contents($file);

        if ($params) {
            $replacements = [];
            foreach ($params as $key => $value) {
                $replacements['{{ ' . (string)$key . ' }}'] = (string)$value;
            }
            $content = strtr($content, $replacements);
        }

        $response = $response->withHeader('Content-Type', 'text/html; charset=utf-8');
        $response->getBody()->write($content);

        return $response;
    }
}
