<?php

declare(strict_types=1);

namespace App\Application\Subscription\DTO\Response;

use App\Application\Subscription\DTO\RequiredPropertiesTrait;
use App\Domain\Subscription\ValueObject\Email;
use App\Domain\Subscription\ValueObject\Repo;

final readonly class ListSubscriptionsDTO implements ResponseDTOInterface
{
    use RequiredPropertiesTrait;

    private const string PROP_EMAIL = 'email';
    private const string PROP_REPO = 'repo';
    private const string PROP_CONFIRMED = 'confirmed';
    private const string PROP_LAST_SEEN_TAG = 'last_seen_tag';

    protected const array REQUIRED_PROPERTIES = [
        self::PROP_EMAIL,
        self::PROP_REPO,
        self::PROP_CONFIRMED,
        self::PROP_LAST_SEEN_TAG,
    ];

    public function __construct(
        public Email $email,
        public Repo $repo,
        public bool $confirmed,
        public ?string $lastSeenTag
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        self::validate(self::REQUIRED_PROPERTIES, $data);

        return new self(
            new Email((string)$data[self::PROP_EMAIL]),
            new Repo((string)$data[self::PROP_REPO]),
            (bool)$data[self::PROP_CONFIRMED],
            (string)$data[self::PROP_LAST_SEEN_TAG]
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            self::PROP_EMAIL => $this->email->value,
            self::PROP_REPO => $this->repo->value,
            self::PROP_CONFIRMED => $this->confirmed,
            self::PROP_LAST_SEEN_TAG => $this->lastSeenTag,
        ];
    }
}
