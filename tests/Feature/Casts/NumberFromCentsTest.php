<?php

declare(strict_types=1);

namespace Worksome\Number\Tests\Feature\Casts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Worksome\Number\Casts\NumberFromCents;
use Worksome\Number\Exceptions\ValueTypeMismatchException;
use Worksome\Number\MonetaryAmount;
use Worksome\Number\Number;
use Worksome\Number\Percentage;

/** @property Number $value */
class TestMoneyAsCents extends Model
{
    public $table = 'test_money';

    public $timestamps = false;

    protected $guarded = [];

    public function casts(): array
    {
        return [
            'value_number' => NumberFromCents::class,
            'value_monetary_amount' => NumberFromCents::using(MonetaryAmount::class),
            'value_percentage' => NumberFromCents::using(Percentage::class),
        ];
    }

    public static function castKeys(): array
    {
        return array_keys(new self()->casts());
    }
}

dataset('test keys', function () {
    return TestMoneyAsCents::castKeys();
});

beforeEach(function () {
    Schema::create('test_money', function (Blueprint $table) {
        $table->increments('id');

        foreach (TestMoneyAsCents::castKeys() as $key) {
            $table->integer($key)->nullable();
        }
    });
});

it('casts to the correct class', function (string $key) {
    $class = match ($key) {
        'value_number' => Number::class,
        'value_monetary_amount' => MonetaryAmount::class,
        'value_percentage' => Percentage::class,
    };

    $money = TestMoneyAsCents::create([
        $key => $class::of(8125.00),
    ]);

    expect($money->{$key})->toBeInstanceOf($class);
})->with('test keys');

it('throws if input is not correct class', function () {
    $this->expectException(ValueTypeMismatchException::class);

    TestMoneyAsCents::create([
        'value_monetary_amount' => Number::of(8125.00),
    ]);
});

it('can cast from cents to a number', function () {
    $money = TestMoneyAsCents::create([
        'value_number' => Number::of(8125.00),
    ]);

    expect($money->value_number)
        ->toBeInstanceOf(Number::class)
        ->toEqual(Number::of(8125));
});

it('throws an exception for non-number values', function () {
    TestMoneyAsCents::create([
        'value_number' => 12345,
    ]);
})->throws(ValueTypeMismatchException::class, 'The given cents value is not a Number instance');

it('configures the correct bindings for decimals', function ($value, int $binding) {
    $bindings = [];

    $this->app->make('db')->listen(static function ($query) use (&$bindings) {
        $bindings = $query->bindings;
    });

    TestMoneyAsCents::create([
        'value_number' => Number::of($value),
    ]);

    $this->assertSame([$binding], $bindings);
})->with([
    [1, 100],
    [1.0, 100],
    [100, 10000],
    [8125.00, 812500],
    [1000000.00, 100000000],
    [1000000.99, 100000099],
    ['1', 100],
]);

it('supports null values', function () {
    $money = TestMoneyAsCents::create([
        'value_number' => Number::of(123),
    ]);
    $money->value_number = null;

    expect($money->value_number)->toBeNull();
});
