<?php

declare(strict_types=1);

namespace Worksome\Number;

use Brick\Math\BigDecimal;
use Brick\Math\BigNumber;
use Brick\Math\RoundingMode;
use InvalidArgumentException;

class Number
{
    protected const ALLOWED_ROUNDING_MODES = [
        RoundingMode::UNNECESSARY, RoundingMode::UP, RoundingMode::DOWN, RoundingMode::HALF_UP, RoundingMode::HALF_DOWN,
        RoundingMode::HALF_CEILING, RoundingMode::HALF_FLOOR, RoundingMode::HALF_EVEN,
    ];

    final protected function __construct(protected BigDecimal $value, protected int $roundingMode)
    {
        $this->validate();
    }

    /** @see RoundingMode for available rounding mode constants */
    public static function of(string|int|float|BigNumber|Number $value, ?int $roundingMode = null): static
    {
        if ($value instanceof Number && $roundingMode === null) {
            $roundingMode = $value->getRoundingMode();
        }

        if ($roundingMode === null) {
            $roundingMode = RoundingMode::HALF_EVEN;
        }

        if (! in_array($roundingMode, self::ALLOWED_ROUNDING_MODES)) {
            throw new InvalidArgumentException("An invalid rounding mode \"{$roundingMode}\" was provided");
        }

        if ($value instanceof BigNumber) {
            return new static($value->toBigDecimal(), $roundingMode);
        }

        if ($value instanceof Number) {
            return new static($value->getValue(), $roundingMode);
        }

        return new static(BigDecimal::of($value), $roundingMode);
    }

    public function add(string|int|float|BigNumber|Number $value): Number
    {
        if (! $value instanceof Number) {
            $value = Number::of($value);
        }

        return static::of($this->value->plus($value->value));
    }

    public function sub(string|int|float|BigNumber|Number $value): Number
    {
        if (! $value instanceof Number) {
            $value = Number::of($value);
        }

        return static::of($this->value->minus($value->value));
    }

    public function mul(string|int|float|BigNumber|Number $value): Number
    {
        if (! $value instanceof Number) {
            $value = Number::of($value);
        }

        return static::of($this->value->multipliedBy($value->value));
    }

    public function div(string|int|float|BigNumber|Number $value, int $decimalPlaces = 2): Number
    {
        if (! $value instanceof Number) {
            $value = Number::of($value);
        }

        return static::of($this->value->dividedBy($value->value, $decimalPlaces, $this->getRoundingMode()));
    }

    public function percentage(int|Number $value): Number
    {
        if (! $value instanceof Number) {
            $value = Number::of($value);
        }

        return static::of($this->value->exactlyDividedBy(100)->multipliedBy($value->value));
    }

    public function negate(): Number
    {
        return static::of($this->value->negated());
    }

    public function isLessThan(string|int|float|BigNumber|Number $value): bool
    {
        if (! $value instanceof Number) {
            $value = Number::of($value);
        }

        return $this->value->isLessThan($value->getValue());
    }

    public function isLessThanOrEqualTo(string|int|float|BigNumber|Number $value): bool
    {
        if (! $value instanceof Number) {
            $value = Number::of($value);
        }

        return $this->value->isLessThanOrEqualTo($value->getValue());
    }

    public function isGreaterThan(string|int|float|BigNumber|Number $value): bool
    {
        if (! $value instanceof Number) {
            $value = Number::of($value);
        }

        return $this->value->isGreaterThan($value->getValue());
    }

    public function isGreaterThanOrEqualTo(string|int|float|BigNumber|Number $value): bool
    {
        if (! $value instanceof Number) {
            $value = Number::of($value);
        }

        return $this->value->isGreaterThanOrEqualTo($value->getValue());
    }

    public function isEqualTo(string|int|float|BigNumber|Number $value): bool
    {
        if (! $value instanceof Number) {
            $value = Number::of($value);
        }

        return $this->value->isEqualTo($value->getValue());
    }

    public function isZero(): bool
    {
        return $this->value->isZero();
    }

    public function isNegative(): bool
    {
        return $this->value->isNegative();
    }

    public function isNegativeOrZero(): bool
    {
        return $this->value->isNegativeOrZero();
    }

    public function isPositive(): bool
    {
        return $this->value->isPositive();
    }

    public function isPositiveOrZero(): bool
    {
        return $this->value->isPositiveOrZero();
    }

    public function getValue(): BigDecimal
    {
        return $this->value;
    }

    public function getRoundingMode(): int
    {
        return $this->roundingMode;
    }

    public function toString(): string
    {
        return (string) $this->value;
    }

    public function toFloat(): float
    {
        return $this->value->toFloat();
    }

    public function inCents(): int
    {
        return $this->mul(100)->getValue()->toInt();
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    protected function validate(): void
    {
        //
    }
}
