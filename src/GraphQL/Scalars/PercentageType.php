<?php

declare(strict_types=1);

namespace Worksome\Number\GraphQL\Scalars;

use Brick\Math\Exception\NumberFormatException;
use GraphQL\Error\Error;
use GraphQL\Language\AST\FloatValueNode;
use GraphQL\Language\AST\IntValueNode;
use GraphQL\Language\AST\Node;
use GraphQL\Type\Definition\ScalarType;
use Worksome\Number\Exceptions\BaseException;
use Worksome\Number\Percentage;

final class PercentageType extends ScalarType
{
    public $description = <<<TXT
        The `Percentage` scalar type represents a percentage.
        TXT;

    public function serialize($value)
    {
        return $this->parseValue($value);
    }

    public function parseValue($value)
    {
        try {
            return Percentage::of($value)->toFloat();
        } catch (BaseException|NumberFormatException $exception) {
            throw new Error($exception->getMessage());
        }
    }

    public function parseLiteral(Node $valueNode, ?array $variables = null)
    {
        if (! $valueNode instanceof IntValueNode && ! $valueNode instanceof FloatValueNode) {
            throw new Error("Query error: Can only parse integer or float got: " . $valueNode->kind, [$valueNode]);
        }

        return Percentage::of($valueNode->value)->toFloat();
    }
}
