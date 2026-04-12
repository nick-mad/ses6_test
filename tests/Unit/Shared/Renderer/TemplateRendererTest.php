<?php

namespace App\Test\Unit\Shared\Renderer;

use App\Shared\Renderer\TemplateRenderer;
use Nyholm\Psr7\Response;
use PHPUnit\Framework\TestCase;

class TemplateRendererTest extends TestCase
{
    private string $tempDir;

    protected function setUp(): void
    {
        $this->tempDir = sys_get_temp_dir() . '/templates_' . uniqid();
        mkdir($this->tempDir);
    }

    protected function tearDown(): void
    {
        if (is_dir($this->tempDir)) {
            $files = glob($this->tempDir . '/*');
            foreach ($files as $file) {
                unlink($file);
            }
            rmdir($this->tempDir);
        }
    }

    public function testRenderSuccess(): void
    {
        file_put_contents($this->tempDir . '/test.html', 'Hello {{ name }}!');

        $renderer = new TemplateRenderer($this->tempDir);
        $response = new Response();

        $response = $renderer->render($response, 'test.html', ['name' => 'World']);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('text/html; charset=utf-8', $response->getHeaderLine('Content-Type'));
        $this->assertEquals('Hello World!', (string)$response->getBody());
    }

    public function testRenderNotFound(): void
    {
        $renderer = new TemplateRenderer($this->tempDir);
        $response = new Response();

        $response = $renderer->render($response, 'nonexistent.html');

        $this->assertEquals(500, $response->getStatusCode());
        $this->assertStringContainsString('Template not found', (string)$response->getBody());
    }
}
