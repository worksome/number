<?php

declare(strict_types=1);

use Brick\Math\BigDecimal;
use Pest\Expectation;
use Worksome\Number\Number;
use Worksome\Number\Percentage;

it('can instantiate a Percentage from values', function (string|int|float|BigDecimal|Percentage $value) {
    /** @var Expectation $expectation */
    $expectation = expect(Percentage::of($value))->toBeInstanceOf(Percentage::class);

    $type = gettype($value);
    if ($type == 'double') {
        expect($expectation->value->getValue()->toFloat())->toBeFloat()->toBe($value);
    } elseif ($type == 'string') {
        expect((string) $expectation->value)->toBeString()->toBe("{$value}%");
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
    '`1.0` as Number from string' => Percentage::of('1.0'),
    '`1` as Number from integer' => Percentage::of(1),
    '`0.1` as Number from float' => Percentage::of(0.1),
]);

it('is immutable', function () {
    $number = Percentage::of('123456');

    expect($number->getValue())->toEqual(BigDecimal::of(123456));

    $number->add(Percentage::of(123456));

    expect($number->getValue())->toEqual(BigDecimal::of(123456));

    $number->sub(Percentage::of(123456));

    expect($number->getValue())->toEqual(BigDecimal::of(123456));

    $number->mul(Percentage::of(2));

    expect($number->getValue())->toEqual(BigDecimal::of(123456));

    $number->div(Percentage::of(2));

    expect($number->getValue())->toEqual(BigDecimal::of(123456));
});

it('can get underlying value as string', function (string $number, string $result) {
    expect(Percentage::of($number)->toString())->toEqual($result);
})->with([
    'integers as strings' => ['10', '10%'],
    'integers' => [2, '2%'],
    'floats as strings' => ['0.002', '0.002%'],
    'floats' => [0.002, '0.002%'],
    'large floats as strings' => ['1000000001.1000000001', '1000000001.1000000001%'],
]);
