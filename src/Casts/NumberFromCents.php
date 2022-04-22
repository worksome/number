<?php

declare(strict_types=1);

namespace Worksome\Number\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Worksome\Number\Exceptions\ValueIsNotANumberException;
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
            throw ValueIsNotANumberException::fromCents();
        }

        return $value->inCents();
    }
}
