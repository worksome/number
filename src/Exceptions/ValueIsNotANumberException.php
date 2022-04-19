<?php

declare(strict_types=1);

namespace Worksome\Number\Exceptions;

use InvalidArgumentException;

class ValueIsNotANumberException extends InvalidArgumentException
{
    public static function fromDecimal(): self
    {
        return new self('The given decimal value is not a Number instance');
    }

    public static function fromCents(): self
    {
        return new self('The given cents value is not a Number instance');
    }
}
