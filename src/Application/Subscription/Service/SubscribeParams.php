<?php

declare(strict_types=1);

namespace App\Application\Subscription\Service;

use App\Application\Subscription\DTO\Request\SubscribeDTO;
use App\Domain\Subscription\ValueObject\Email;
use App\Domain\Subscription\ValueObject\Repo;

final readonly class SubscribeParams
{
    public function __construct(
        public Email $email,
        public Repo $repo,
    ) {
    }

    public static function formDTO(SubscribeDTO $dto): self
    {
        return new self(
            email: $dto->email,
            repo: $dto->repo,
        );
    }
}
