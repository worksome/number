<?php

declare(strict_types=1);

use GraphQL\GraphQL;
use GraphQL\Type\Schema;
use GraphQL\Utils\SchemaPrinter;
use Worksome\Number\GraphQL\Types\PercentageType;

use function Spatie\Snapshots\assertMatchesJsonSnapshot;
use function Spatie\Snapshots\assertMatchesTextSnapshot;

it('can resolve fields on a GraphQL Percentage type', function () {
    $numberType = new PercentageType();

    $schema = new Schema(['query' => $numberType]);

    $request = <<<'GQL'
        {
            __type(name: "Percentage") {
                name
                fields {
                    name
                }
            }
        }
    GQL;

    expect(data_get(GraphQL::executeQuery($schema, $request)->toArray(), 'data.__type.name'))->toBe('Percentage');

    assertMatchesJsonSnapshot(
        json_encode(data_get(GraphQL::executeQuery($schema, $request)->toArray(), 'data.__type.fields.*.name'))
    );
});

it('can generate schema for GraphQL Percentage type', function () {
    $type = new PercentageType();

    assertMatchesTextSnapshot(SchemaPrinter::printType($type));
});
