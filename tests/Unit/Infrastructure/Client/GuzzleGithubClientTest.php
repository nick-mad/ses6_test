<?php

namespace App\Test\Unit\Infrastructure\Client;

use App\Domain\Subscription\Exception\RateLimitExceededException;
use App\Domain\Subscription\Exception\RepositoryNotFoundException;
use App\Infrastructure\Client\GuzzleGithubClient;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class GuzzleGithubClientTest extends TestCase
{
    public function testValidateRepositorySuccess(): void
    {
        $mock = new MockHandler([
            new Response(200, [], ''),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $githubClient = new GuzzleGithubClient($client);
        $githubClient->validateRepository('owner/repo');

        $this->assertTrue(true);
    }

    public function testValidateRepositoryNotFound(): void
    {
        $mock = new MockHandler([
            new ClientException('Not Found', new Request('GET', 'repos/owner/repo'), new Response(404)),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $githubClient = new GuzzleGithubClient($client);

        $this->expectException(RepositoryNotFoundException::class);
        $githubClient->validateRepository('owner/repo');
    }

    public function testValidateRepositoryRateLimit(): void
    {
        $mock = new MockHandler([
            new ClientException('Rate Limit', new Request('GET', 'repos/owner/repo'), new Response(403)),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $githubClient = new GuzzleGithubClient($client);

        $this->expectException(RateLimitExceededException::class);
        $githubClient->validateRepository('owner/repo');
    }

    public function testGetLatestTagSuccess(): void
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode([['name' => 'v1.0.0']])),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $githubClient = new GuzzleGithubClient($client);
        $tag = $githubClient->getLatestTag('owner/repo');

        $this->assertEquals('v1.0.0', $tag);
    }

    public function testGetLatestTagEmpty(): void
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode([])),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $githubClient = new GuzzleGithubClient($client);
        $tag = $githubClient->getLatestTag('owner/repo');

        $this->assertNull($tag);
    }
}
