<?php

declare(strict_types=1);

namespace Worksome\Number\GraphQL\Scalars;

use Brick\Math\Exception\NumberFormatException;
use GraphQL\Error\Error;
use GraphQL\Language\AST\FloatValueNode;
use GraphQL\Language\AST\IntValueNode;
use GraphQL\Language\AST\Node;
use GraphQL\Type\Definition\ScalarType;
use Worksome\Number\Exceptions\NumberException;
use Worksome\Number\Number;

abstract class DecimalType extends ScalarType
{
    protected int|null $decimals = null;

    /**
     * @param Number|string|int|float $value
     *
     * @throws Error
     */
    public function serialize($value): float
    {
        return $this->parseValue($value)->toFloat();
    }

    /**
     * @param Number|string|int|float $value
     *
     * @throws Error
     */
    public function parseValue($value): Number
    {
        assert($this->decimals !== null);

        try {
            $number = Number::of($value);
            $this->validateDecimals($number);

            return $number;
        } catch (NumberException|NumberFormatException $exception) {
            throw new Error($exception->getMessage());
        }
    }

    public function parseLiteral(Node $valueNode, array|null $variables = null): Number
    {
        assert($this->decimals !== null);

        if (! $valueNode instanceof IntValueNode && ! $valueNode instanceof FloatValueNode) {
            throw new Error('Query error: Can only parse integer or float. Got: ' . $valueNode->kind, [$valueNode]);
        }

        $number = Number::of($valueNode->value);

        $this->validateDecimals($number);

        return $number;
    }

    /**
     * @throws Error
     */
    private function validateDecimals(Number $number): void
    {
        if ($number->getCurrentDecimalCount() > $this->decimals) {
            throw new Error('Value has more decimal places than allowed: ' . $this->decimals);
        }
    }
}
