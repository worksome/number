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
use Worksome\Number\StrictPercentage;

final class StrictPercentageType extends ScalarType
{
    public string|null $description = <<<TXT
        The `StrictPercentage` scalar type represents a percentage that cannot be less than 0% or greater than 100%.
        TXT;

    /**
     * @param string|int|float $value
     *
     * @throws Error
     */
    public function serialize($value): float
    {
        return $this->parseValue($value)->toFloat();
    }

    /**
     * @param string|int|float $value
     *
     * @throws Error
     */
    public function parseValue($value): StrictPercentage
    {
        try {
            return StrictPercentage::of($value);
        } catch (NumberException|NumberFormatException $exception) {
            throw new Error($exception->getMessage());
        }
    }

    public function parseLiteral(Node $valueNode, array|null $variables = null): StrictPercentage
    {
        if (! $valueNode instanceof IntValueNode && ! $valueNode instanceof FloatValueNode) {
            throw new Error('Query error: Can only parse integer or float. Got: ' . $valueNode->kind, [$valueNode]);
        }

        return StrictPercentage::of($valueNode->value);
    }
}
