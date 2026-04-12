<?php

declare(strict_types=1);

namespace App\Infrastructure\Client;

use App\Domain\Subscription\Client\GithubClientInterface;
use App\Domain\Subscription\Exception\RateLimitExceededException;
use App\Domain\Subscription\Exception\RepositoryNotFoundException;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use RuntimeException;

final readonly class GuzzleGithubClient implements GithubClientInterface
{
    public function __construct(private ClientInterface $client)
    {
    }

    public function validateRepository(string $repo): void
    {
        try {
            $this->client->request('GET', "repos/$repo");
        } catch (ClientException $e) {
            $response = $e->getResponse();
            if ($response->getStatusCode() === 404) {
                throw new RepositoryNotFoundException();
            }
            if ($response->getStatusCode() === 429 || $response->getStatusCode() === 403) {
                throw new RateLimitExceededException();
            }
            throw $e;
        } catch (GuzzleException $e) {
            throw new RuntimeException('GitHub API error: ' . $e->getMessage(), 0, $e);
        }
    }
    public function getLatestTag(string $repo): ?string
    {
        try {
            $response = $this->client->request('GET', "repos/$repo/tags");
            /** @var array<int, array<string, mixed>> $tags */
            $tags = json_decode($response->getBody()->getContents(), true);

            if (empty($tags)) {
                return null;
            }

            return (string)($tags[0]['name'] ?? '');
        } catch (ClientException $e) {
            $response = $e->getResponse();
            if ($response->getStatusCode() === 404) {
                throw new RepositoryNotFoundException();
            }
            if ($response->getStatusCode() === 429 || $response->getStatusCode() === 403) {
                throw new RateLimitExceededException();
            }
            throw $e;
        } catch (GuzzleException $e) {
            throw new RuntimeException('GitHub API error: ' . $e->getMessage(), 0, $e);
        }
    }
}
