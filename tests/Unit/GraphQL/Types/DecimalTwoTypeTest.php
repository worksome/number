<?php

declare(strict_types=1);

use GraphQL\Utils\SchemaPrinter;
use Worksome\Number\GraphQL\Scalars\DecimalTwoType as DecimalTwo;

it('can serialize', function ($value, $expected) {
    $serialized = (new DecimalTwo())->serialize($value);

    expect($serialized)->toBeFloat()->toBe($expected);
})->with([
    'integer to float' => [100, 100.0],
    'float to float' => [100.0, 100.0],
    'string to float' => ['100', 100.0],
]);

it('can generate schema for GraphQL DecimalTwo scalar', function () {
    $type = new DecimalTwo();

    expect(SchemaPrinter::printType($type))->toMatchSnapshot();
});
