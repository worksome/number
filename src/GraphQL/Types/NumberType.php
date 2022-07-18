<?php

declare(strict_types=1);

namespace Worksome\Number\GraphQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Worksome\Number\Number;

final class NumberType extends ObjectType
{
    public function __construct()
    {
        parent::__construct([
            'name' => 'Number',
            'description' => 'A generic representation of a number.',
            'fields' => [
                'isZero' => [
                    'type' => Type::nonNull(Type::boolean()),
                    'description' => 'Whether the number is zero.',
                ],
                'isNegative' => [
                    'type' => Type::nonNull(Type::boolean()),
                    'description' => 'Whether the number is negative.',
                ],
                'isNegativeOrZero' => [
                    'type' => Type::nonNull(Type::boolean()),
                    'description' => 'Whether the number is negative or zero.',
                ],
                'isPositive' => [
                    'type' => Type::nonNull(Type::boolean()),
                    'description' => 'Whether the number is positive.',
                ],
                'isPositiveOrZero' => [
                    'type' => Type::nonNull(Type::boolean()),
                    'description' => 'Whether the number is positive or zero.',
                ],
                'toString' => [
                    'type' => Type::nonNull(Type::string()),
                    'description' => 'Retrieve the number as a string representation.',
                ],
                'toFloat' => [
                    'type' => Type::nonNull(Type::float()),
                    'description' => 'Retrieve the number as a float.',
                ],
                'inCents' => [
                    'type' => Type::nonNull(Type::int()),
                    'description' => 'Retrieve the number in cents (only applies to monetary values).',
                ],
            ],
            'resolveField' => function (float|int|string $number, array $args, $context, ResolveInfo $info) {
                $number = Number::of($number);

                if (method_exists($number, $info->fieldName)) {
                    return $number->{$info->fieldName}();
                }

                if (property_exists($number, $info->fieldName)) {
                    return $number->{$info->fieldName};
                }

                return null;
            },
        ]);
    }
}
