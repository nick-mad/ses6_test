<?php

declare(strict_types=1);

namespace App\Domain\Subscription\Client;

use App\Domain\Subscription\Exception\RateLimitExceededException;
use App\Domain\Subscription\Exception\RepositoryNotFoundException;
use RuntimeException;

interface GithubClientInterface
{
    /**
     * @param string $repo Full repository name (owner/repo)
     * @throws RepositoryNotFoundException
     * @throws RateLimitExceededException
     * @throws RuntimeException
     * @return void
     */
    public function validateRepository(string $repo): void;

    /**
     * @param string $repo Full repository name (owner/repo)
     * @throws RepositoryNotFoundException
     * @throws RateLimitExceededException
     * @throws RuntimeException
     * @return string|null Latest tag name
     */
    public function getLatestTag(string $repo): ?string;
}
