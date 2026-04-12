<?php

declare(strict_types=1);

namespace App\Application\Subscription\DTO\Request;

use App\Application\Subscription\DTO\RequiredPropertiesTrait;
use App\Domain\Subscription\ValueObject\Token;
use Psr\Http\Message\ServerRequestInterface;

final readonly class TokenDTO implements RequestDTOInterface
{
    use RequiredPropertiesTrait;

    private const string PROP_TOKEN = 'token';

    protected const array REQUIRED_PROPERTIES = [
        self::PROP_TOKEN,
    ];

    public function __construct(
        public Token $token,
    ) {
    }

    /**
     * @param array<string, mixed> $args
     * @param ServerRequestInterface $request
     */
    public static function fromRequest(ServerRequestInterface $request, array $args = []): self
    {
        $data = array_merge((array)$request->getParsedBody(), $args);
        self::validate(self::REQUIRED_PROPERTIES, $data);

        return new self(
            new Token((string)$data[self::PROP_TOKEN]),
        );
    }
}
