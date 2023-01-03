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

    expect(Number::of($number, $mode)->div(Number::of(3, $mode), 0)->getValue()->toInt())->toEqual($result);
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

it(
    'can multiply numbers',
    function (string|int|float $number, string|int|float|Number $change, string|int|float $result) {
        expect(Number::of($number)->mul($change)->getValue())->toEqual(BigDecimal::of($result));
    }
)->with([
    'integers as strings as Number' => ['2', Number::of('10'), '20'],
    'integers as strings' => ['2', '10', '20'],
    'integers as Number' => [2, Number::of(10), 20],
    'integers' => [2, 10, 20],
    'floats as strings as Number' => ['0.001', Number::of('0.002'), '0.000002'],
]);

it(
    'can divide numbers',
    function (string|int|float $number, string|int|float|Number $change, string|int|float $result) {
        expect(Number::of($number)->div($change)->toString())->toEqual($result);
    }
)->with([
    'integers as strings as Number' => ['10', Number::of('2'), '5.00'],
    'integers as strings' => ['10', '2', '5.00'],
    'integers as Number' => [10, Number::of(2), '5.00'],
    'integers' => [10, 2, '5.00'],
    'floats as strings as Number' => ['10.02', Number::of('2'), '5.01'],
]);

it(
    'can get percentage of numbers',
    function (string|int|float $number, int|Number $percentage, string|int|float $result) {
        expect(Number::of($number)->percentage($percentage)->getValue())->toEqual(BigDecimal::of($result));
    }
)->with([
    'integers as strings as Number' => ['500', Number::of('10'), '50'],
    'integers as strings' => ['500', '10', '50'],
    'integers as Number' => [500, Number::of(10), 50],
    'integers' => [500, 10, 50],
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

it('can check whether number is less than', function (int $number, int $lessThan, bool $result) {
    expect(Number::of($number)->isLessThan($lessThan))->toBe($result);
})->with([
    '10 less than 15' => [10, 15, true],
    '15 not less than 10' => [15, 10, false],
]);

it('can check whether number is less than or equal to', function (int $number, int $greaterThan, bool $result) {
    expect(Number::of($number)->isLessThanOrEqualTo($greaterThan))->toBe($result);
})->with([
    '10 less than / equal to 15' => [10, 15, true],
    '10 less than / equal to 10' => [10, 10, true],
    '15 not less than / equal to 10' => [15, 10, false],
]);

it('can check whether number is greater than', function (int $number, int $greaterThan, bool $result) {
    expect(Number::of($number)->isGreaterThan($greaterThan))->toBe($result);
})->with([
    '15 greater than 10' => [15, 10, true],
    '10 not greater than 15' => [10, 15, false],
]);

it('can check whether number is greater than or equal to', function (int $number, int $greaterThan, bool $result) {
    expect(Number::of($number)->isGreaterThanOrEqualTo($greaterThan))->toBe($result);
})->with([
    '15 greater than / equal to 10' => [15, 10, true],
    '10 greater than / equal to 10' => [10, 10, true],
    '10 not greater than / equal to 15' => [10, 15, false],
]);

it('can check whether number is equal to', function (int $number, int $greaterThan, bool $result) {
    expect(Number::of($number)->isGreaterThanOrEqualTo($greaterThan))->toBe($result);
})->with([
    '15 equal to 10' => [10, 10, true],
    '10 not equal to 15' => [10, 15, false],
]);

it('can check whether number is zero', function (int $number, bool $result) {
    expect(Number::of($number)->isZero())->toBe($result);
})->with([
    '0 is zero' => [0, true],
    '10 is not zero' => [10, false],
    '1.0 is not zero' => [1.0, false],
    '-1.0 is not zero' => [-1.0, false],
]);

it('can check whether number is negative', function (int $number, bool $result) {
    expect(Number::of($number)->isNegative())->toBe($result);
})->with([
    '-1 is negative' => [-1, true],
    '-10000 is negative' => [-10000, true],
    '0 is not negative' => [0, false],
    '1 is not negative' => [1, false],
    '10000 is not negative' => [10000, false],
]);

it('can check whether number is negative or zero', function (int $number, bool $result) {
    expect(Number::of($number)->isNegativeOrZero())->toBe($result);
})->with([
    '-1 is negative or zero' => [-1, true],
    '-10000 is negative or zero' => [-10000, true],
    '0 is not negative or zero' => [0, true],
    '1 is not negative or zero' => [1, false],
    '10000 is not negative or zero' => [10000, false],
]);

it('can check whether number is positive', function (int $number, bool $result) {
    expect(Number::of($number)->isPositive())->toBe($result);
})->with([
    '1 is positive' => [1, true],
    '10000 is positive' => [10000, true],
    '0 is not positive' => [0, false],
    '-1 is not positive' => [-1, false],
    '-10000 is not positive' => [-10000, false],
]);

it('can check whether number is positive or zero', function (int $number, bool $result) {
    expect(Number::of($number)->isPositiveOrZero())->toBe($result);
})->with([
    '1 is positive or zero' => [1, true],
    '10000 is positive or zero' => [10000, true],
    '0 is positive or zero' => [0, true],
    '-1 is not positive or zero' => [-1, false],
    '-10000 is not positive or zero' => [-10000, false],
]);

it('can check if equal to', function (int|string|float $number, int|string|float $comparison) {
    expect(Number::of($number)->isEqualTo(Number::of($comparison)))->toBe(true);
})->with([
    '1 is 1' => [1, 1],
    '10.01 is 1001' => [1001, 1001],
    '0 is 0' => [0, 0],
    '-101.10 is -10110' => [-101.10, -101.10],
    '101.001 as string is 101.001' => ['101.001', 101.001],
    '-101.001 as string is -101.001' => ['-101.001', -101.001],
]);

it('can get Number in cents', function (int|string|float $number, int $result) {
    expect(Number::of($number)->inCents())->toBe($result);
})->with([
    '1.00 as string is 1' => ['1.00', 100],
    '10.01 is 1001' => [10.01, 1001],
    '0 is 0' => [0.00, 0],
    '101.10 as string is 10110' => ['101.10', 10110],
    '1111111101.99 as string is 111111110199' => ['1111111101.99', 111111110199],
    '1.10 as string is 110' => ['1.10', 110],
    '-1.10 is -110' => [-1.10, -110],
    '-1111111101.99 is -111111110199' => [-1111111101.99, -111111110199],
]);

it('can specify the number of decimal places for division', function ($result, $expectedString, $expectedFloat) {
    expect($result)
        ->toString()->toBe($expectedString)
        ->toFloat()->toBe($expectedFloat);
})->with([
    [Number::of(20, RoundingMode::HALF_UP)->div(100), '0.20', 0.2],
    [Number::of(10.5, RoundingMode::HALF_UP)->div(2), '5.25', 5.25],
    [Number::of(5.2, RoundingMode::HALF_UP)->div(4.5, 3), '1.156', 1.156],
    [Number::of(4.1234, RoundingMode::HALF_UP)->div(2.2, 3), '1.874', 1.874],
]);
