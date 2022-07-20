<?php

declare(strict_types=1);

namespace Worksome\Number\GraphQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Worksome\Number\StrictPercentage;

final class StrictPercentageType extends ObjectType
{
    public function __construct()
    {
        parent::__construct([
            'name' => 'StrictPercentage',
            'description' => 'A strict representation of a percentage.',
            'fields' => [
                'isZero' => [
                    'type' => Type::nonNull(Type::boolean()),
                    'description' => 'Whether the percentage is zero.',
                ],
                'isNegative' => [
                    'type' => Type::nonNull(Type::boolean()),
                    'description' => 'Whether the percentage is negative.',
                ],
                'isNegativeOrZero' => [
                    'type' => Type::nonNull(Type::boolean()),
                    'description' => 'Whether the percentage is negative or zero.',
                ],
                'isPositive' => [
                    'type' => Type::nonNull(Type::boolean()),
                    'description' => 'Whether the percentage is positive.',
                ],
                'isPositiveOrZero' => [
                    'type' => Type::nonNull(Type::boolean()),
                    'description' => 'Whether the percentage is positive or zero.',
                ],
                'toString' => [
                    'type' => Type::nonNull(Type::string()),
                    'description' => 'Retrieve the percentage as a string representation.',
                ],
                'toFloat' => [
                    'type' => Type::nonNull(Type::float()),
                    'description' => 'Retrieve the percentage as a float.',
                ],
            ],
            'resolveField' => function (float|int|string $percentage, array $args, $context, ResolveInfo $info) {
                $percentage = StrictPercentage::of($percentage);

                if (method_exists($percentage, $info->fieldName)) {
                    return $percentage->{$info->fieldName}();
                }

                if (property_exists($percentage, $info->fieldName)) {
                    return $percentage->{$info->fieldName};
                }

                return null;
            },
        ]);
    }
}
