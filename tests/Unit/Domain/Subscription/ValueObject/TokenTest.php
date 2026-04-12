<?php

namespace App\Test\Unit\Domain\Subscription\ValueObject;

use App\Domain\Subscription\ValueObject\Token;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class TokenTest extends TestCase
{
    public function testTokenSuccess(): void
    {
        $token = new Token('valid-token_123');
        $this->assertEquals('valid-token_123', $token->value);
    }

    public function testTokenEmptyThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Token('');
    }

    public function testTokenTooLongThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Token(str_repeat('a', 65));
    }

    public function testTokenInvalidCharsThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Token('invalid token!');
    }
}
