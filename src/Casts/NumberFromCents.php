<?php

declare(strict_types=1);

namespace Worksome\Number\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use InvalidArgumentException;
use Worksome\Number\Number;

class NumberFromCents implements CastsAttributes
{
    /** @param int $value */
    public function get($model, string $key, $value, array $attributes)
    {
        return Number::of($value)->div(100);
    }

    /** @param Number $value */
    public function set($model, string $key, $value, array $attributes)
    {
        if (! $value instanceof Number) {
            throw new InvalidArgumentException('The given value is not a Number instance.');
        }

        return $value->mul(100)->getValue()->toInt();
    }
}
