<?php

declare(strict_types=1);

namespace Worksome\Number\Tests\Feature\Casts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Worksome\Number\Casts\NumberFromCents;
use Worksome\Number\Exceptions\ValueIsNotANumberException;
use Worksome\Number\Number;

beforeEach(function () {
    Schema::create('test_money', function (Blueprint $table) {
        $table->increments('id');
        $table->integer('value')->nullable();
    });
});

it('can cast from cents to a number', function () {
    $money = TestMoneyAsCents::create([
        'value' => Number::of(8125.00),
    ]);

    expect($money->value)
        ->toBeInstanceOf(Number::class)
        ->toEqual(Number::of(8125));
});

it('throws an exception for non-number values', function () {
    TestMoneyAsCents::create([
        'value' => 12345,
    ]);
})->throws(ValueIsNotANumberException::class, 'The given cents value is not a Number instance');

it('configures the correct bindings for decimals', function ($value, int $binding) {
    $bindings = [];

    $this->app->make('db')->listen(static function ($query) use (&$bindings) {
        $bindings = $query->bindings;
    });

    TestMoneyAsCents::create([
        'value' => Number::of($value),
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
        'value' => Number::of(123),
    ]);
    $money->value = null;

    expect($money->value)->toBeNull();
});

/** @property Number $value */
class TestMoneyAsCents extends Model
{
    public $table = 'test_money';

    public $timestamps = false;

    protected $guarded = [];

    public $casts = [
        'value' => NumberFromCents::class,
    ];
}
