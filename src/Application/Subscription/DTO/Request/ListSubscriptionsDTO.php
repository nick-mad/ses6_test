<?php

declare(strict_types=1);

namespace App\Application\Subscription\DTO\Request;

use App\Application\Subscription\DTO\RequiredPropertiesTrait;
use App\Domain\Subscription\ValueObject\Email;
use Psr\Http\Message\ServerRequestInterface;

final readonly class ListSubscriptionsDTO implements RequestDTOInterface
{
    use RequiredPropertiesTrait;

    private const string PROP_EMAIL = 'email';


    protected const array REQUIRED_PROPERTIES = [
        self::PROP_EMAIL,
    ];

    public function __construct(
        public Email $email,
    ) {
    }

    /**
     * @param array<string, mixed> $args
     * @param ServerRequestInterface $request
     */
    public static function fromRequest(ServerRequestInterface $request, array $args = []): self
    {
        $params = $request->getQueryParams();
        self::validate(self::REQUIRED_PROPERTIES, $params);

        return new self(
            new Email((string)$params[self::PROP_EMAIL])
        );
    }
}
