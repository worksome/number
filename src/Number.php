<?php

declare(strict_types=1);

namespace Worksome\Number;

use Brick\Math\BigDecimal;
use Brick\Math\BigNumber;
use Brick\Math\RoundingMode;

class Number
{
    final protected function __construct(protected BigDecimal $value)
    {
        $this->validate();
    }

    public static function of(string|int|float|BigNumber|Number $value): static
    {
        if ($value instanceof BigNumber) {
            return new static($value->toBigDecimal());
        }

        if ($value instanceof Number) {
            return new static($value->getValue());
        }

        return new static(BigDecimal::of($value));
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

        return static::of($this->value->dividedBy($value->value, $decimalPlaces, RoundingMode::HALF_UP));
    }

    public function percentage(string|int|float|BigNumber|Number $value): Number
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

    public function format(int $decimals, bool $europeanStyle = false): string
    {
        if ($europeanStyle) {
            return number_format($this->toFloat(), $decimals, ',', '.');
        }

        return number_format($this->toFloat(), $decimals);
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    protected function validate(): void
    {
    }
}
