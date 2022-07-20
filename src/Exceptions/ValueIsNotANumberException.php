<?php

declare(strict_types=1);

namespace Worksome\Number\Exceptions;

class ValueIsNotANumberException extends InvalidValueException
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
