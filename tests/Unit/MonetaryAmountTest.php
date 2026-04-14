<?php

declare(strict_types=1);

use Brick\Math\BigDecimal;
use Worksome\Number\Exceptions\MonetaryAmountDecimalCountException;
use Worksome\Number\MonetaryAmount;
use Worksome\Number\Number;

it('can instantiate a MonetaryAmount from values', function (mixed $value) {
    expect(MonetaryAmount::of($value))->toBeInstanceOf(MonetaryAmount::class);
})->with([
    '`10.00` as string' => ['10.00'],
    '`1.99` as string' => ['1.99'],
    '`0.01` as string' => ['0.01'],
    '`100` as int' => [100],
    '`10` as int' => [10],
    '`99.99` as float' => [99.99],
    '`10.00` as BigDecimal' => [BigDecimal::of('10.00')],
    '`1.99` as MonetaryAmount' => [MonetaryAmount::of('1.99')],
]);

it('rounds arithmetic operations to 2 decimal places', function () {
    $amount = MonetaryAmount::of('10.00');

    expect($amount->div(3)->toString())->toBe('3.33');
    expect($amount->mul('0.333')->toString())->toBe('3.33');
});

it('can add monetary amounts', function (mixed $amount1, mixed $amount2, string $expected) {
    expect(MonetaryAmount::of($amount1)->add($amount2)->toString())->toBe($expected);
})->with([
    'simple addition' => ['10.00', '5.00', '15.00'],
    'decimal addition' => ['10.50', '5.25', '15.75'],
    'cents addition' => ['0.99', '0.01', '1.00'],
    'mixed types' => ['10.50', 5, '15.50'],
]);

it('can subtract monetary amounts', function (mixed $amount1, mixed $amount2, string $expected) {
    expect(MonetaryAmount::of($amount1)->sub($amount2)->toString())->toBe($expected);
})->with([
    'simple subtraction' => ['10.00', '5.00', '5.00'],
    'decimal subtraction' => ['10.50', '5.25', '5.25'],
    'cents subtraction' => ['1.00', '0.99', '0.01'],
    'mixed types' => ['10.50', 5, '5.50'],
    'negative result' => ['5.00', '10.00', '-5.00'],
]);

it('can multiply monetary amounts', function (mixed $amount, mixed $multiplier, string $expected) {
    expect(MonetaryAmount::of($amount)->mul($multiplier)->toString())->toBe($expected);
})->with([
    'multiply by integer' => ['10.00', 2, '20.00'],
    'multiply by decimal' => ['10.00', '1.5', '15.00'],
    'multiply by cents' => ['99.99', 2, '199.98'],
    'multiply with rounding' => ['10.00', '0.333', '3.33'],
    'rounds automatically' => ['2.50', '3.35', '8.38'],
]);

it('can divide monetary amounts', function (mixed $amount, mixed $divisor, string $expected) {
    expect(MonetaryAmount::of($amount)->div($divisor)->toString())->toBe($expected);
})->with([
    'divide by integer' => ['10.00', 2, '5.00'],
    'divide by decimal' => ['15.00', '1.5', '10.00'],
    'divide with rounding down' => ['10.00', 3, '3.33'],
    'divide with rounding up' => ['10.00', 6, '1.67'],
]);

it('can calculate percentage of monetary amount', function (mixed $amount, mixed $percentage, string $expected) {
    expect(MonetaryAmount::of($amount)->percentage($percentage)->toString())->toBe($expected);
})->with([
    '10% of 100.00' => ['100.00', 10, '10.00'],
    '50% of 100.00' => ['100.00', 50, '50.00'],
    '15% of 200.00' => ['200.00', 15, '30.00'],
    '5.5% of 100.00' => ['100.00', '5.5', '5.50'],
]);

it('can negate monetary amounts', function (mixed $amount, string $expected) {
    expect(MonetaryAmount::of($amount)->negate()->toString())->toBe($expected);
})->with([
    'positive to negative' => ['10.00', '-10.00'],
    'negative to positive' => ['-10.00', '10.00'],
    'zero stays zero' => ['0.00', '0.00'],
]);

it('can round monetary amounts', function () {
    $amount = MonetaryAmount::of('10.13');

    expect($amount->round(2)->toString())->toBe('10.13');
    expect($amount->round(1)->toString())->toBe('10.10');
    expect($amount->round(0)->toString())->toBe('10.00');
});

it('can compare monetary amounts', function () {
    $ten = MonetaryAmount::of('10.00');
    $five = MonetaryAmount::of('5.00');
    $anotherTen = MonetaryAmount::of('10.00');

    expect($five->isLessThan($ten))->toBeTrue();
    expect($ten->isLessThan($five))->toBeFalse();

    expect($five->isLessThanOrEqualTo($ten))->toBeTrue();
    expect($ten->isLessThanOrEqualTo($anotherTen))->toBeTrue();

    expect($ten->isGreaterThan($five))->toBeTrue();
    expect($five->isGreaterThan($ten))->toBeFalse();

    expect($ten->isGreaterThanOrEqualTo($five))->toBeTrue();
    expect($ten->isGreaterThanOrEqualTo($anotherTen))->toBeTrue();

    expect($ten->isEqualTo($anotherTen))->toBeTrue();
    expect($ten->isEqualTo($five))->toBeFalse();
});

it('can check sign of monetary amounts', function () {
    $zero = MonetaryAmount::of('0.00');

    expect($zero)
        ->isZero()->toBeTrue()
        ->isNegative()->toBeFalse()
        ->isNegativeOrZero()->toBeTrue()
        ->isPositive()->toBeFalse()
        ->isPositiveOrZero()->toBeTrue();

    $positive = MonetaryAmount::of('10.00');

    expect($positive)
        ->isZero()->toBeFalse()
        ->isNegative()->toBeFalse()
        ->isNegativeOrZero()->toBeFalse()
        ->isPositive()->toBeTrue()
        ->isPositiveOrZero()->toBeTrue();

    $negative = MonetaryAmount::of('-10.00');

    expect($negative)
        ->isZero()->toBeFalse()
        ->isNegative()->toBeTrue()
        ->isNegativeOrZero()->toBeTrue()
        ->isPositive()->toBeFalse()
        ->isPositiveOrZero()->toBeFalse();
});

it('can convert from cents', function (int $amount, MonetaryAmount $expected) {
    expect(MonetaryAmount::fromCents($amount)->format(2))->toEqual($expected->format(2));
})->with([
    '100 cents is 1.00' => [100, MonetaryAmount::of(1.00)],
    '1050 cents is 10.50 cents' => [1050, MonetaryAmount::of(10.50)],
    '9999 cents is 99.99 cents' => [9999, MonetaryAmount::of(99.99)],
    '1 cents is 0.01' => [1, MonetaryAmount::of(0.01)],
    '-550 cents is -5.50' => [-550, MonetaryAmount::of(-5.50)],
]);

it('can convert to cents', function (mixed $amount, int $expected) {
    expect(MonetaryAmount::of($amount)->toCents())->toBe($expected);
})->with([
    '1.00 is 100 cents' => ['1.00', 100],
    '10.50 is 1050 cents' => ['10.50', 1050],
    '99.99 is 9999 cents' => ['99.99', 9999],
    '0.01 is 1 cent' => ['0.01', 1],
    '-5.50 is -550 cents' => ['-5.50', -550],
]);

it('can format monetary amounts', function (mixed $amount, int $decimals, string $expected) {
    expect(MonetaryAmount::of($amount)->format($decimals))->toBe($expected);
})->with([
    '2 decimals' => ['1234.56', 2, '1,234.56'],
    '1 decimals' => ['1234.5', 2, '1,234.50'],
    '0 decimals' => ['1234.56', 0, '1,235'],
]);

it('can format monetary amounts in European style', function (mixed $amount, int $decimals, string $expected) {
    expect(MonetaryAmount::of($amount)->format($decimals, true))->toBe($expected);
})->with([
    '2 decimals' => ['1234.56', 2, '1.234,56'],
    '1 decimal' => ['1234.5', 2, '1.234,50'],
    '0 decimals' => ['1234.56', 0, '1.235'],
]);

it('can convert to string', function (mixed $amount, string $expected) {
    expect(MonetaryAmount::of($amount)->toString())->toBe($expected);
    expect((string) MonetaryAmount::of($amount))->toBe($expected);
})->with([
    'simple amount' => ['10.00', '10.00'],
    'decimal amount' => ['99.99', '99.99'],
    'negative amount' => ['-50.00', '-50.00'],
]);

it('can convert to float', function (mixed $amount, float $expected) {
    expect(MonetaryAmount::of($amount)->toFloat())->toBe($expected);
})->with([
    'simple amount' => ['10.00', 10.0],
    'decimal amount' => ['99.99', 99.99],
    'negative amount' => ['-50.00', -50.0],
]);

it('is immutable', function () {
    $amount = MonetaryAmount::of('100.00');

    expect($amount->toString())->toBe('100.00');

    $amount->add('50.00');
    expect($amount->toString())->toBe('100.00');

    $amount->sub('50.00');
    expect($amount->toString())->toBe('100.00');

    $amount->mul(2);
    expect($amount->toString())->toBe('100.00');

    $amount->div(2);
    expect($amount->toString())->toBe('100.00');
});

it('returns MonetaryAmount instance from arithmetic operations', function () {
    $amount = MonetaryAmount::of('10.00');

    expect($amount->add('5.00'))->toBeInstanceOf(MonetaryAmount::class);
    expect($amount->sub('5.00'))->toBeInstanceOf(MonetaryAmount::class);
    expect($amount->mul(2))->toBeInstanceOf(MonetaryAmount::class);
    expect($amount->div(2))->toBeInstanceOf(MonetaryAmount::class);
    expect($amount->negate())->toBeInstanceOf(MonetaryAmount::class);
    expect($amount->round(1))->toBeInstanceOf(MonetaryAmount::class);
});

it('can interoperate with Number class', function () {
    $monetary = MonetaryAmount::of('10.00');
    $number = Number::of('5.00');

    expect($monetary->add($number)->toString())->toBe('15.00');
    expect($monetary->sub($number)->toString())->toBe('5.00');
    expect($monetary->mul($number)->toString())->toBe('50.00');
    expect($monetary->div($number)->toString())->toBe('2.00');
});

it('handles edge cases', function (mixed $amount, string $expected) {
    expect(MonetaryAmount::of($amount)->toString())->toBe($expected);
})->with([
    'zero' => ['0', '0.00'],
    'very large amount' => ['999999999.99', '999999999.99'],
    'scientific notation' => ['1e2', '100.00'],
]);

it('throw if not using two decimal places', function () {
    $this->expectException(MonetaryAmountDecimalCountException::class);

    MonetaryAmount::of('10.12', 3);
});

it('throw if not using value with two decimal places', function () {
    $this->expectException(MonetaryAmountDecimalCountException::class);

    MonetaryAmount::of('10.123');
});

it('can get sum of monetary amounts', function () {
    $sum = MonetaryAmount::sum([
        MonetaryAmount::of('1.00'),
        MonetaryAmount::of('1.00'),
        MonetaryAmount::of('1.00'),
    ]);

    expect($sum)->toEqual(MonetaryAmount::of('3.00'));
});
