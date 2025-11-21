<?php

declare(strict_types=1);

namespace Worksome\Number\Tests\Feature\Casts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Worksome\Number\Casts\NumberFromDecimal;
use Worksome\Number\Exceptions\ValueTypeMismatchException;
use Worksome\Number\MonetaryAmount;
use Worksome\Number\Number;
use Worksome\Number\Percentage;

/** @property Number $value */
class TestMoneyAsDecimal extends Model
{
    public $table = 'test_money';

    public $timestamps = false;

    protected $guarded = [];

    public function casts(): array
    {
        return [
            'value_number' => NumberFromDecimal::class,
            'value_number_4' => NumberFromDecimal::using(4),
            'value_monetary_amount' => NumberFromDecimal::using(class: MonetaryAmount::class),
            'value_percentage' => NumberFromDecimal::using(class: Percentage::class),
        ];
    }

    public static function castKeys(): array
    {
        return array_keys(new self()->casts());
    }
}

dataset('test keys', function () {
    return TestMoneyAsDecimal::castKeys();
});

beforeEach(function () {
    Schema::create('test_money', function (Blueprint $table) {
        $table->increments('id');

        foreach (TestMoneyAsDecimal::castKeys() as $key) {
            $table->decimal($key)->nullable();
        }
    });
});

it('casts to the correct class', function (string $key) {
    $class = match ($key) {
        'value_number',
        'value_number_4' => Number::class,
        'value_monetary_amount' => MonetaryAmount::class,
        'value_percentage' => Percentage::class,
    };

    $money = TestMoneyAsDecimal::create([
        $key => $class::of(8125.10),
    ]);

    // Refresh the model to use the casts
    $money->refresh();

    expect($money->{$key})->toBeInstanceOf($class);

    expect(match ($key) {
        'value_number' => 2,
        'value_number_4' => 4,
        'value_monetary_amount' => 2,
        'value_percentage' => 2,
    })->toBe($money->{$key}->getMaxDecimalCount());
})->with('test keys');

it('throws if input is not correct class', function () {
    $this->expectException(ValueTypeMismatchException::class);

    TestMoneyAsDecimal::create([
        'value_monetary_amount' => Number::of(8125.00),
    ]);
});

it('can cast from decimals to a number', function () {
    $money = TestMoneyAsDecimal::create([
        'value_number' => Number::of(100.22),
    ]);

    expect($money->value_number)
        ->toBeInstanceOf(Number::class)
        ->toEqual(Number::of(100.22));
});

it('throws an exception for non-number values', function () {
    TestMoneyAsDecimal::create([
        'value_number' => 12345,
    ]);
})->throws(ValueTypeMismatchException::class, 'The given decimal value is not a Number instance');

it('configures the correct bindings for decimals', function ($value, string $binding) {
    $bindings = [];

    $this->app->make('db')->listen(static function ($query) use (&$bindings) {
        $bindings = $query->bindings;
    });

    TestMoneyAsDecimal::create([
        'value_number' => Number::of($value),
    ]);

    $this->assertSame([$binding], $bindings);
})->with([
    [1, '1.00'],
    [1.00, '1.00'],
    [100.22, '100.22'],
    [9999999999.99, '9999999999.99'],
    ['9999999999.99', '9999999999.99'],
]);

it('supports null values', function () {
    $money = TestMoneyAsDecimal::create([
        'value_number' => Number::of(123),
    ]);
    $money->value = null;

    expect($money->value)->toBeNull();
});
