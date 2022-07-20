<?php

declare(strict_types=1);

use GraphQL\GraphQL;
use GraphQL\Type\Schema;
use GraphQL\Utils\SchemaPrinter;
use Worksome\Number\GraphQL\Types\NumberType;

use function Spatie\Snapshots\assertMatchesJsonSnapshot;
use function Spatie\Snapshots\assertMatchesTextSnapshot;

it('can resolve fields on a GraphQL Number type', function () {
    $numberType = new NumberType();

    $schema = new Schema(['query' => $numberType]);

    $request = <<<'GQL'
        {
            __type(name: "Number") {
                name
                fields {
                    name
                }
            }
        }
    GQL;

    expect(data_get(GraphQL::executeQuery($schema, $request)->toArray(), 'data.__type.name'))->toBe('Number');

    assertMatchesJsonSnapshot(
        json_encode(data_get(GraphQL::executeQuery($schema, $request)->toArray(), 'data.__type.fields.*.name'))
    );
});

it('can generate schema for GraphQL Number type', function () {
    $numberType = new NumberType();

    assertMatchesTextSnapshot(SchemaPrinter::printType($numberType));
});
