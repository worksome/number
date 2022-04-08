<?php

declare(strict_types=1);

use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use Pest\Expectation;
use Worksome\Number\Number;

it('can instantiate a Number from values', function (string|int|float|BigDecimal|Number $value) {
    /** @var Expectation $expectation */
    $expectation = expect(Number::of($value))->toBeInstanceOf(Number::class);

    $type = gettype($value);
    if ($type == 'double') {
        expect($expectation->value->getValue()->toFloat())->toBeFloat()->toBe($value);
    } elseif ($type == 'string') {
        expect((string) $expectation->value)->toBeString()->toBe($value);
    } elseif ($type == 'integer') {
        expect($expectation->value->getValue()->toInt())->toBeInt()->toBe($value);
    } elseif ($type == 'object' && $expectation->value instanceof BigDecimal) {
        expect($expectation->value->getValue())->toEqual($value);
    } elseif ($type == 'object' && $expectation->value instanceof Number) {
        expect($expectation->value->getValue())->toBeInstanceOf(BigDecimal::class);
    } else {
        $this->fail('An invalid type was provided in the dataset');
    }
})->with([
    '`1.0` as string' => '1.0',
    '`1` as string' => '1',
    '`0.01` as string' => '0.01',
    '`1` as int' => 1,
    '`10` as int' => 10,
    '`0.1` as float' => 0.1,
    '`0.00000001` as float' => 0.00000001,
    '`1.0` as BigDecimal from string' => BigDecimal::of('1.0'),
    '`1` as BigDecimal from integer' => BigDecimal::of(1),
    '`0.1` as BigDecimal from float' => BigDecimal::of(0.1),
    '`1.0` as Number from string' => Number::of('1.0'),
    '`1` as Number from integer' => Number::of(1),
    '`0.1` as Number from float' => Number::of(0.1),
]);

it('is immutable', function () {
    $number = Number::of('123456');

    expect($number->getValue())->toEqual(BigDecimal::of(123456));

    $number->add(Number::of(123456));

    expect($number->getValue())->toEqual(BigDecimal::of(123456));

    $number->sub(Number::of(123456));

    expect($number->getValue())->toEqual(BigDecimal::of(123456));

    $number->mul(Number::of(2));

    expect($number->getValue())->toEqual(BigDecimal::of(123456));

    $number->div(Number::of(2));

    expect($number->getValue())->toEqual(BigDecimal::of(123456));
});

it('can handle rounding numbers', function (int|null $mode, int $result, ?Number $existing = null) {
    $number = $existing instanceof Number ? Number::of($existing) : Number::of(1000, $mode);

    expect(Number::of($number, $mode)->div(Number::of(3, $mode))->getValue()->toInt())->toEqual($result);
})->with([
     'existing' => [null, 334, Number::of(1000, RoundingMode::UP)],
    // 'default' => [null, 334],
    // 'unnecessary' => [RoundingMode::UNNECESSARY, 334],
    'rounding up' => [RoundingMode::UP, 334],
    'rounding down' => [RoundingMode::DOWN, 333],
    'rounding half up' => [RoundingMode::HALF_UP, 333],
    'rounding half down' => [RoundingMode::HALF_DOWN, 333],
]);

it('can add numbers', function (string|int|float $number, string|int|float $change, string|int|float $result) {
    expect(Number::of($number)->add(Number::of($change))->getValue())->toEqual(BigDecimal::of($result));
})->with([
    'integers as strings' => ['123456', '123456', '246912'],
    'integers' => [123456, 123456, 246912],
    'floats as strings' => ['0.001', '0.002', '0.003'],
    'floats' => [0.001, 0.002, 0.003],
]);

it('can subtract numbers', function (string|int|float $number, string|int|float $change, string|int|float $result) {
    expect(Number::of($number)->sub($change)->getValue())->toEqual(BigDecimal::of($result));
})->with([
    'integers as strings as Number' => ['123456', Number::of('123456'), '0'],
    'integers as strings' => ['123456', '123456', '0'],
    'integers as Number' => [123456, Number::of(123456), 0],
    'integers' => [123456, 123456, 0],
    'floats as strings as Number' => ['0.002', Number::of('0.001'), '0.001'],
    'floats as strings' => ['0.002', '0.001', '0.001'],
    'floats as Number' => [0.002, Number::of(0.001), 0.001],
    'floats' => [0.002, 0.001, 0.001],
]);

it('can multiply numbers', function (string|int|float $number, string|int|float|Number $change, string|int|float $result) {
    expect(Number::of($number)->mul($change)->getValue())->toEqual(BigDecimal::of($result));
})->with([
    'integers as strings as Number' => ['2', Number::of('10'), '20'],
    'integers as strings' => ['2', '10', '20'],
    'integers as Number' => [2, Number::of(10), 20],
    'integers' => [2, 10, 20],
    'floats as strings as Number' => ['0.001', Number::of('0.002'), '0.000002'],
]);

it('can negate numbers', function (string|int|float $number, string|int|float $result) {
    expect(Number::of($number)->negate()->getValue())->toEqual(BigDecimal::of($result));
})->with([
    'integers as strings' => ['10', '-10'],
    'integers' => [2, -2],
    'floats as strings' => ['0.002', '-0.002'],
    'floats' => [0.002, -0.002],
]);

it('can get underlying value as string', function (string $number, string $result) {
    expect(Number::of($number)->toString())->toEqual($result);
})->with([
    'integers as strings' => ['10', '10'],
    'integers' => [2, 2],
    'floats as strings' => ['0.002', '0.002'],
    'floats' => [0.002, 0.002],
    'large floats as strings' => ['1000000001.1000000001', '1000000001.1000000001'],
]);

it('can get underlying value as float', function (string $number, float $result) {
    expect(Number::of($number)->toFloat())->toEqual($result);
})->with([
    'integers as strings' => ['10', 10],
    'integers' => [2, 2],
    'floats as strings' => ['0.002', 0.002],
    'floats' => [0.002, 0.002],
    'large floats as strings' => ['1000000001.1000000001', 1000000001.1000000001],
]);
