<?php

declare(strict_types=1);

namespace App\Application\Subscription\Service;

use App\Domain\Subscription\Client\GithubClientInterface;
use App\Domain\Subscription\Exception\EmailAlreadySubscribedException;
use App\Domain\Subscription\Exception\TokenNotFoundException;
use App\Domain\Subscription\Repository\SubscriptionRepositoryInterface;
use App\Domain\Subscription\Service\NotifierInterface;
use Throwable;

final readonly class SubscribeService implements SubscribeServiceInterface
{
    public function __construct(
        private SubscriptionRepositoryInterface $repository,
        private GithubClientInterface $githubClient,
        private NotifierInterface $notifier
    ) {
    }

    public function subscribe(SubscribeParams $params): void
    {
        // Проверяем существование репозитория через GitHub API
        $this->githubClient->validateRepository($params->repo->value);

        // Проверяем наличие активной подписки
        if ($this->repository->isSubscribed($params->email->value, $params->repo->value)) {
            throw new EmailAlreadySubscribedException();
        }

        $token = bin2hex(random_bytes(32));
        $tag = $this->githubClient->getLatestTag($params->repo->value);

        $this->repository->save($params->email->value, $params->repo->value, $token, $tag);

        // Отправляем письмо для подтверждения
        try {
            $this->notifier->sendConfirmation($params->email->value, $params->repo->value, $token);
        } catch (Throwable $e) {
            // Если письмо не ушло (например, ошибка SMTP), мы не должны прерывать процесс подписки
            // так как запись в БД уже создана.
        }
    }

    public function confirm(string $token): void
    {
        $subscription = $this->repository->findByToken($token);
        if (!$subscription) {
            throw new TokenNotFoundException();
        }

        $this->repository->confirm($token);
    }

    public function unsubscribe(string $token): void
    {
        $subscription = $this->repository->findByToken($token);
        if (!$subscription) {
            throw new TokenNotFoundException();
        }

        $this->repository->unsubscribe($token);
    }
}
