<?php

declare(strict_types=1);

use GraphQL\GraphQL;
use GraphQL\Type\Schema;
use GraphQL\Utils\SchemaPrinter;
use Worksome\Number\GraphQL\Types\StrictPercentageType;

use function Spatie\Snapshots\assertMatchesJsonSnapshot;
use function Spatie\Snapshots\assertMatchesTextSnapshot;

it('can resolve fields on a GraphQL StrictPercentage type', function () {
    $numberType = new StrictPercentageType();

    $schema = new Schema(['query' => $numberType]);

    $request = <<<'GQL'
        {
            __type(name: "StrictPercentage") {
                name
                fields {
                    name
                }
            }
        }
    GQL;

    expect(data_get(GraphQL::executeQuery($schema, $request)->toArray(), 'data.__type.name'))->toBe('StrictPercentage');

    assertMatchesJsonSnapshot(
        json_encode(data_get(GraphQL::executeQuery($schema, $request)->toArray(), 'data.__type.fields.*.name'))
    );
});

it('can generate schema for GraphQL StrictPercentage type', function () {
    $type = new StrictPercentageType();

    assertMatchesTextSnapshot(SchemaPrinter::printType($type));
});
