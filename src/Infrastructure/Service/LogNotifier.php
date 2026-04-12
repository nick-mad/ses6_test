<?php

declare(strict_types=1);

namespace App\Infrastructure\Service;

use App\Domain\Subscription\Service\NotifierInterface;
use Psr\Log\LoggerInterface;

final readonly class LogNotifier implements NotifierInterface
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    public function notify(string $email, string $repo, string $newTag): void
    {
        // Simple log for now, can be extended to use PHPMailer or similar
        $this->logger->info(sprintf('Sending email to %s about new release %s of %s', $email, $newTag, $repo));

        // Simulating email sending
        $subject = sprintf('Новий реліз для %s', $repo);
        $message = sprintf('Привіт! З\'явився новий реліз %s для репозиторію %s.', $newTag, $repo);
        // mail($email, $subject, $message);
    }

    public function sendConfirmation(string $email, string $repo, string $token): void
    {
        $this->logger->info(
            sprintf('Sending confirmation email to %s for repo %s with token %s', $email, $repo, $token)
        );

        // Simulating email sending
        $subject = sprintf('Підтвердження підписки на %s', $repo);
        $confirmationUrl = sprintf('/api/confirm/%s', $token); // URL can be full-qualified if needed
        $message = sprintf(
            'Будь ласка, підтвердіть вашу підписку на релізи %s за посиланням: %s',
            $repo,
            $confirmationUrl
        );
        // mail($email, $subject, $message);
    }
}
