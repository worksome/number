<?php

declare(strict_types=1);

use GraphQL\Error\Error;
use GraphQL\Utils\SchemaPrinter;
use Worksome\Number\GraphQL\Scalars\StrictPercentageType as StrictPercentage;
use function Spatie\Snapshots\assertMatchesTextSnapshot;

it('can serialize', function ($value, $expected) {
    $serialized = (new StrictPercentage())->serialize($value);

    expect($serialized)->toBeFloat()->toBe($expected);
})->with([
    'integer to float' => [100, 100.0],
    'float to float' => [100.0, 100.0],
    'string to float' => ['100', 100.0],
]);

it('throws an error with invalid value', function ($value) {
    (new StrictPercentage())->serialize($value);
})->throws(Error::class)->with([
    'less than 0' => -1,
    'greater than 100' => 101,
    'invalid string' => 'abcdefg',
]);

it('can generate schema for GraphQL StrictPercentage scalar', function () {
    $type = new StrictPercentage();

    assertMatchesTextSnapshot(SchemaPrinter::printType($type));
});
