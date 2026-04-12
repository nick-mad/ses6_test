<?php

declare(strict_types=1);

namespace App\Application\Subscription\DTO;

use Symfony\Component\OptionsResolver\Exception\InvalidArgumentException;

trait RequiredPropertiesTrait
{
    /**
     * @param array<int|string, mixed> $required
     * @param array<string, mixed> $passed
     */
    protected static function validate(array $required, array $passed): void
    {
        if (isset($required[0])) {
            $required = array_flip($required);
        }

        $diff = array_diff_key($required, $passed);

        if (count($diff)) {
            throw new InvalidArgumentException(
                "Properties are missed: " . implode(', ', array_keys($diff))
            );
        }
    }
}
