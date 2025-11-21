<?php

declare(strict_types=1);

namespace Worksome\Number\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Worksome\Number\Exceptions\ValueTypeMismatchException;
use Worksome\Number\Number;

/**
 * @template TClass of Number
 *
 * @implements CastsAttributes<Number, Number>
 */
class NumberFromDecimal implements CastsAttributes
{
    /** @param class-string<TClass> $class */
    public function __construct(
        private int $decimals = 2,
        private string $class = Number::class,
    ) {
    }

    /** @param class-string<Number> $class */
    public static function using(
        int $decimals = 2,
        string $class = Number::class,
    ): string {
        return static::class . ':' . implode(',', func_get_args());
    }

    /**
     * @param float|string|null $value
     *
     * @return TClass|null
     */
    public function get($model, string $key, $value, array $attributes)
    {
        if ($value === null) {
            return null;
        }

        return ($this->class)::of($value, $this->decimals);
    }

    /** @param  TClass|null  $value */
    public function set($model, string $key, $value, array $attributes)
    {
        if ($value === null) {
            return null;
        }

        if (! $value instanceof $this->class) {
            throw ValueTypeMismatchException::fromDecimal(
                class_basename($this->class),
            );
        }

        return number_format(
            $value->getValue()->toFloat(),
            $this->decimals,
            '.',
            '',
        );
    }
}
