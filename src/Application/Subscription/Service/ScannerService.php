<?php

declare(strict_types=1);

namespace App\Application\Subscription\Service;

use App\Domain\Subscription\Client\GithubClientInterface;
use App\Domain\Subscription\Exception\RateLimitExceededException;
use App\Domain\Subscription\Repository\SubscriptionRepositoryInterface;
use App\Domain\Subscription\Service\NotifierInterface;
use Exception;
use Psr\Log\LoggerInterface;

final readonly class ScannerService implements ScannerServiceInterface
{
    public function __construct(
        private SubscriptionRepositoryInterface $repository,
        private GithubClientInterface $githubClient,
        private NotifierInterface $notifier,
        private LoggerInterface $logger
    ) {
    }

    public function scan(): void
    {
        $this->logger->info('Starting release scan...');
        $subscriptions = $this->repository->findAllActive();

        $repoTags = [];

        foreach ($subscriptions as $subscription) {
            $repo = $subscription['repo'];
            $email = $subscription['email'];
            $currentTag = $subscription['last_seen_tag'];

            $this->repository->updateLastScannedAt((int)$subscription['id']);

            try {
                if (!isset($repoTags[$repo])) {
                    $repoTags[$repo] = $this->githubClient->getLatestTag($repo);
                }

                $latestTag = $repoTags[$repo];

                if ($latestTag === null) {
                    $this->logger->debug(sprintf('No tags found for repository %s. Skipping.', $repo));
                    continue;
                }

                if ($currentTag === null || version_compare($latestTag, $currentTag, '>')) {
                    $this->logger->info(
                        sprintf(
                            'New release found for %s: %s (current: %s). Notifying %s',
                            $repo,
                            $latestTag,
                            $currentTag ?? 'none',
                            $email
                        )
                    );

                    $this->notifier->notify($email, $repo, $latestTag);
                    $this->repository->updateLastSeenTag((int)$subscription['id'], $latestTag);
                }
            } catch (RateLimitExceededException $e) {
                $this->logger->error('GitHub API Rate Limit exceeded. Stopping scan.');
                break;
            } catch (Exception $e) {
                $this->logger->error(
                    sprintf('Error scanning repo %s for %s: %s', $repo, $email, $e->getMessage())
                );
            }
        }

        $this->logger->info('Release scan finished.');
    }
}
