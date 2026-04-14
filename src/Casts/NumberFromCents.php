<?php

declare(strict_types=1);

namespace Worksome\Number\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Worksome\Number\Exceptions\ValueTypeMismatchException;
use Worksome\Number\MonetaryAmount;

/**
 * @template TClass of MonetaryAmount
 *
 * @implements CastsAttributes<MonetaryAmount, MonetaryAmount>
 */
class NumberFromCents implements CastsAttributes
{
    /** @param class-string<TClass> $class */
    public function __construct(
        private string $class = MonetaryAmount::class,
    ) {
    }

    /** @param class-string<MonetaryAmount> $class */
    public static function using(
        string $class = MonetaryAmount::class,
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

    /** @param MonetaryAmount|null $value */
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

        return $value->toCents();
    }
}
