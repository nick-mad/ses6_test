<?php

declare(strict_types=1);

namespace App\Infrastructure\Service;

use App\Domain\Subscription\Service\NotifierInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Throwable;

final readonly class SmtpNotifier implements NotifierInterface
{
    /**
     * @param array<string, mixed> $config
     * @param LoggerInterface $logger
     * @param MailerInterface $mailer
     */
    public function __construct(
        private LoggerInterface $logger,
        private MailerInterface $mailer,
        private array $config
    ) {
    }

    public function notify(string $email, string $repo, string $newTag): void
    {
        $subject = sprintf('Новий реліз для %s', $repo);
        $message = sprintf('Привіт! З\'явився новий реліз %s для репозиторію %s.', $newTag, $repo);

        $this->send($email, $subject, $message);
    }

    public function sendConfirmation(string $email, string $repo, string $token): void
    {
        $subject = sprintf('Підтвердження підписки на %s', $repo);
        $baseUrl = $this->config['base_url'] ?? 'http://localhost:8080';
        $confirmationUrl = sprintf('%s/api/confirm/%s', rtrim($baseUrl, '/'), $token);
        $message = sprintf(
            'Будь ласка, підтвердіть вашу підписку на релізи %s за посиланням: %s',
            $repo,
            $confirmationUrl
        );

        $this->send($email, $subject, $message);
    }

    private function send(string $to, string $subject, string $message): void
    {
        $fromEmail = $this->config['from_email'] ?? 'noreply@example.com';
        $fromName = $this->config['from_name'] ?? 'Release Notifier';

        $email = (new Email())
            ->from(sprintf('%s <%s>', $fromName, $fromEmail))
            ->to($to)
            ->subject($subject)
            ->text($message);

        try {
            $maskedDsn = $this->config['dsn'] ?? 'null';
            if ($maskedDsn !== 'null') {
                $maskedDsn = preg_replace('/:(.*)@/', ':******@', $maskedDsn);
            }
            $this->logger->info(
                sprintf('Attempting to send email to %s via Symfony Mailer with DSN: %s', $to, $maskedDsn)
            );
            $this->mailer->send($email);
            $this->logger->info(sprintf('Email sent successfully to %s with subject "%s"', $to, $subject));
        } catch (TransportExceptionInterface $e) {
            $this->logger->error(sprintf('Email sending failed for %s: %s', $to, $e->getMessage()));
            $this->logger->error($e->getTraceAsString());
        } catch (Throwable $e) {
            $this->logger->error(
                sprintf('Unexpected error during email sending to %s: %s', $to, $e->getMessage())
            );
            $this->logger->error($e->getTraceAsString());
        }
    }
}
