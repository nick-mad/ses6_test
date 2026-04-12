<?php

declare(strict_types=1);

namespace App\Domain\Subscription\Service;

interface NotifierInterface
{
    public function notify(string $email, string $repo, string $newTag): void;

    public function sendConfirmation(string $email, string $repo, string $token): void;
}
