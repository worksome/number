<?php

declare(strict_types=1);

namespace Worksome\Number\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use InvalidArgumentException;
use Worksome\Number\Number;

class NumberFromDecimal implements CastsAttributes
{
    public function __construct(
        private int $decimals = 2,
        private string $decimalSeparator = '.',
        private string $thousandsSeparator = '',
    ) {
    }

    /** @param  float  $value */
    public function get($model, string $key, $value, array $attributes)
    {
        return Number::of($value);
    }

    /** @param  Number  $value */
    public function set($model, string $key, $value, array $attributes)
    {
        if (! $value instanceof Number) {
            throw new InvalidArgumentException('The given value is not a Number instance.');
        }

        return number_format(
            $value->getValue()->toFloat(),
            $this->decimals,
            $this->decimalSeparator,
            $this->thousandsSeparator,
        );
    }
}
