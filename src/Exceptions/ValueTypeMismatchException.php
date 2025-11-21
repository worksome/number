<?php

declare(strict_types=1);

namespace Worksome\Number\Exceptions;

use InvalidArgumentException;

class ValueTypeMismatchException extends InvalidArgumentException implements NumberException
{
    public static function fromDecimal(string $type): self
    {
        return new self("The given decimal value is not a {$type} instance");
    }

    public static function fromCents(string $type): self
    {
        return new self("The given cents value is not a {$type} instance");
    }
}
