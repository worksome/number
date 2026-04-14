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
class NumberFromCents implements CastsAttributes
{
    /** @param class-string<TClass> $class */
    public function __construct(
        private string $class = Number::class,
    ) {
    }

    /** @param class-string<Number> $class */
    public static function using(
        string $class = Number::class,
    ): string {
        // @phpstan-ignore argument.type
        return static::class . ':' . implode(',', array_map(fn ($value) => strval($value), func_get_args()));
    }

    /**
     * @param int|null $value
     *
     * @return TClass|null
     */
    public function get($model, string $key, $value, array $attributes)
    {
        if ($value === null) {
            return null;
        }

        return ($this->class)::of($value)->div(100);
    }

    /** @param Number|null $value */
    public function set($model, string $key, $value, array $attributes)
    {
        if ($value === null) {
            return null;
        }

        if (! $value instanceof $this->class) {
            throw ValueTypeMismatchException::fromCents(
                class_basename($this->class),
            );
        }

        // @phpstan-ignore method.deprecated (This class will be changed to only support subclasses of `MonetaryAmount`)
        return $value->inCents();
    }
}
