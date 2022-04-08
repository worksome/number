<?php

declare(strict_types=1);

namespace Worksome\Number;

use Brick\Math\BigDecimal;
use Brick\Math\BigNumber;
use Brick\Math\RoundingMode;
use InvalidArgumentException;

class Number
{
    private const ALLOWED_ROUNDING_MODES = [
        RoundingMode::UNNECESSARY, RoundingMode::UP, RoundingMode::DOWN, RoundingMode::HALF_UP, RoundingMode::HALF_DOWN,
    ];

    private function __construct(private BigDecimal $value, private int $roundingMode)
    {
    }

    /** @see RoundingMode for available rounding mode constants */
    public static function of(string|int|float|BigNumber|Number $value, ?int $roundingMode = null): Number
    {
        if ($value instanceof Number && $roundingMode === null) {
            $roundingMode = $value->getRoundingMode();
        }

        if ($roundingMode === null) {
            $roundingMode = RoundingMode::UNNECESSARY;
        }

        if (! in_array($roundingMode, self::ALLOWED_ROUNDING_MODES)) {
            throw new InvalidArgumentException("An invalid rounding mode \"{$roundingMode}\" was provided");
        }

        if ($value instanceof BigNumber) {
            return new self($value->toBigDecimal(), $roundingMode);
        }

        if ($value instanceof Number) {
            return new self($value->getValue(), $roundingMode);
        }

        return new self(BigDecimal::of($value), $roundingMode);
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

    public function div(string|int|float|BigNumber|Number $value): Number
    {
        if (! $value instanceof Number) {
            $value = Number::of($value);
        }

        return static::of($this->value->dividedBy($value->value, null, $this->getRoundingMode()));
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

    public function __toString(): string
    {
        return $this->toString();
    }
}