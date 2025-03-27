<?php

declare(strict_types=1);

namespace Worksome\Number\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Worksome\Number\Exceptions\ValueIsNotANumberException;
use Worksome\Number\Number;

/** @implements CastsAttributes<Number, Number> */
class NumberFromDecimal implements CastsAttributes
{
    public function __construct(
        private int $decimals = 2,
        private string $decimalSeparator = '.',
        private string $thousandsSeparator = '',
    ) {
    }

    /** @param  float|string|null  $value */
    public function get($model, string $key, $value, array $attributes)
    {
        if ($value === null) {
            return null;
        }

        return Number::of($value);
    }

    /** @param  Number|null  $value */
    public function set($model, string $key, $value, array $attributes)
    {
        if ($value === null) {
            return null;
        }

        // @phpstan-ignore instanceof.alwaysTrue (we type hint `Number` as it should be one, but there's still a possibility that it isn't.)
        if (! $value instanceof Number) {
            throw ValueIsNotANumberException::fromDecimal();
        }

        return number_format(
            $value->getValue()->toFloat(),
            $this->decimals,
            $this->decimalSeparator,
            $this->thousandsSeparator,
        );
    }
}
