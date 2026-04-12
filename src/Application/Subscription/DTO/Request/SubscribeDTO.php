<?php

declare(strict_types=1);

namespace App\Application\Subscription\DTO\Request;

use App\Application\Subscription\DTO\RequiredPropertiesTrait;
use App\Domain\Subscription\ValueObject\Email;
use App\Domain\Subscription\ValueObject\Repo;
use Psr\Http\Message\ServerRequestInterface;

final readonly class SubscribeDTO implements RequestDTOInterface
{
    use RequiredPropertiesTrait;

    private const string PROP_EMAIL = 'email';
    private const string PROP_REPO = 'repo';

    protected const array REQUIRED_PROPERTIES = [
        self::PROP_EMAIL,
        self::PROP_REPO,
    ];

    public function __construct(
        public Email $email,
        public Repo $repo,
    ) {
    }

    /**
     * @param array<string, mixed> $args
     * @param ServerRequestInterface $request
     */
    public static function fromRequest(ServerRequestInterface $request, array $args = []): self
    {
        $data = (array)$request->getParsedBody();
        self::validate(self::REQUIRED_PROPERTIES, $data);

        return new self(
            new Email((string)$data[self::PROP_EMAIL]),
            new Repo((string)$data[self::PROP_REPO]),
        );
    }
}
