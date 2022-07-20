<?php

declare(strict_types=1);

use Brick\Math\BigDecimal;
use Pest\Expectation;
use Worksome\Number\Exceptions\InvalidValueException;
use Worksome\Number\Number;
use Worksome\Number\StrictPercentage;

it('can instantiate a Strict Percentage from values', function (string|int|float|BigDecimal|StrictPercentage $value) {
    /** @var Expectation $expectation */
    $expectation = expect(StrictPercentage::of($value))->toBeInstanceOf(StrictPercentage::class);

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
    '`1.0` as Number from string' => StrictPercentage::of('1.0'),
    '`1` as Number from integer' => StrictPercentage::of(1),
    '`0.1` as Number from float' => StrictPercentage::of(0.1),
]);

it('throws an exception when an invalid percentage is provided',
    function (string|int|float|BigDecimal|StrictPercentage $value) {
        /** @var Expectation $expectation */
        StrictPercentage::of($value);
    })->with([
    'too large (string)' => '101',
    'too small (string)' => '-1',
    'too large (int)' => 101,
    'too small (int)' => -1,
    'too large (float)' => 101.1,
    'too small (float)' => -1.1,
])->throws(InvalidValueException::class);

it('is immutable', function () {
    $number = StrictPercentage::of('1');

    expect($number->getValue())->toEqual(BigDecimal::of(1));

    $number->add(StrictPercentage::of(1));

    expect($number->getValue())->toEqual(BigDecimal::of(1));

    $number->sub(StrictPercentage::of(1));

    expect($number->getValue())->toEqual(BigDecimal::of(1));

    $number->mul(StrictPercentage::of(1));

    expect($number->getValue())->toEqual(BigDecimal::of(1));

    $number->div(StrictPercentage::of(1));

    expect($number->getValue())->toEqual(BigDecimal::of(1));
});

it('can get underlying value as string', function (string $number, string $result) {
    expect(StrictPercentage::of($number)->toString())->toEqual($result);
})->with([
    'integers as strings' => ['10', '10%'],
    'integers' => [2, '2%'],
    'floats as strings' => ['0.002', '0.002%'],
    'floats' => [0.002, '0.002%'],
    'large floats as strings' => ['1.1000000001', '1.1000000001%'],
]);
