<?php

declare(strict_types=1);

namespace Worksome\Number;

use Brick\Math\BigDecimal;
use Brick\Math\BigNumber;
use Brick\Math\RoundingMode;

class Number
{
    protected int|null $decimals = null;

    protected RoundingMode $roundingMode = RoundingMode::HalfUp;

    final protected function __construct(
        protected BigDecimal $value,
        int|null $decimals = null,
    ) {
        if ($decimals !== null) {
            $this->decimals = $decimals;
        }

        $this->validate();
    }

    public static function of(string|int|float|BigNumber|Number $value, int|null $decimals = null): static
    {
        if ($value instanceof BigNumber) {
            return new static($value->toBigDecimal(), $decimals);
        }

        if ($value instanceof Number) {
            return new static($value->getValue(), $decimals);
        }

        return new static(BigDecimal::of($value), $decimals);
    }

    public function add(string|int|float|BigNumber|Number $value): static
    {
        if (! $value instanceof Number) {
            $value = Number::of($value);
        }

        return $this->roundToConfiguredDecimals(
            static::of($this->value->plus($value->value), $this->decimals)
        );
    }

    public function sub(string|int|float|BigNumber|Number $value): static
    {
        if (! $value instanceof Number) {
            $value = Number::of($value);
        }

        return $this->roundToConfiguredDecimals(
            static::of($this->value->minus($value->value), $this->decimals)
        );
    }

    public function mul(string|int|float|BigNumber|Number $value): static
    {
        if (! $value instanceof Number) {
            $value = Number::of($value);
        }

        return $this->roundToConfiguredDecimals(
            static::of($this->value->multipliedBy($value->value), $this->decimals)
        );
    }

    public function div(string|int|float|BigNumber|Number $value): static
    {
        if (! $value instanceof Number) {
            $value = Number::of($value);
        }

        return $this->roundToConfiguredDecimals(
            static::of(
                $this->value->dividedBy($value->value, scale: $this->decimals ?? 2, roundingMode: $this->roundingMode),
                $this->decimals
            )
        );
    }

    public function round(int $scale): static
    {
        return static::of($this->value->toScale($scale, $this->roundingMode), $this->decimals);
    }

    public function percentage(string|int|float|BigNumber|Number $value): static
    {
        if (! $value instanceof Number) {
            $value = Number::of($value);
        }

        return static::of($this->value->dividedByExact(100)->multipliedBy($value->value), $this->decimals);
    }

    public function negate(): static
    {
        return static::of($this->value->negated(), $this->decimals);
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
        return (string) $this->roundToConfiguredDecimals($this)->value;
    }

    public function toFloat(): float
    {
        return $this->value->toFloat();
    }

    public function inCents(): int
    {
        return $this->mul(100)->getValue()->toInt();
    }

    /**
     * Get the minimum number of decimals required to represent this number at its current value.
     */
    public function getCurrentDecimalCount(): int
    {
        $parts = explode('.', $this->toString());

        // If there's no decimal part or only one part (no decimal point)
        if (count($parts) === 1 || empty($parts[1])) {
            return 0;
        }

        $decimal = rtrim($parts[1], '0');

        return strlen($decimal);
    }

    /**
     * Get the maximum number of decimals configured for this number and its operations.
     */
    public function getMaxDecimalCount(): int
    {
        return $this->decimals ?? $this->value->getScale();
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

    private function roundToConfiguredDecimals(Number $value): static
    {
        if ($this->decimals !== null) {
            return $value->round($this->decimals); // @phpstan-ignore return.type
        }

        return $value; // @phpstan-ignore return.type
    }
}
