<?php

declare(strict_types=1);

namespace Worksome\Number\Tests\Feature\Casts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use InvalidArgumentException;
use Worksome\Number\Casts\NumberFromDecimal;
use Worksome\Number\Number;

beforeEach(function () {
    Schema::create('test_money', function (Blueprint $table) {
        $table->increments('id');
        $table->decimal('value')->nullable();
    });
});

it('can cast from decimals to a number', function () {
    $money = TestMoneyAsDecimal::create([
        'value' => Number::of(100.22),
    ]);

    expect($money->value)
        ->toBeInstanceOf(Number::class)
        ->toEqual(Number::of(100.22));
});

it('throws an exception for non-number values', function () {
    TestMoneyAsDecimal::create([
        'value' => 12345,
    ]);
})->throws(InvalidArgumentException::class, 'The given value is not a Number instance');

it('configures the correct bindings for decimals', function ($value, string $binding) {
    $bindings = [];

    $this->app->make('db')->listen(static function ($query) use (&$bindings) {
        $bindings = $query->bindings;
    });

    TestMoneyAsDecimal::create([
        'value' => Number::of($value),
    ]);

    $this->assertSame([$binding], $bindings);
})->with([
    [1, '1.00'],
    [1.00, '1.00'],
    [100.22, '100.22'],
    [9999999999.99, '9999999999.99'],
    ['9999999999.99', '9999999999.99'],
]);

/** @property Number $value */
class TestMoneyAsDecimal extends Model
{
    public $table = 'test_money';
    public $timestamps = false;
    protected $guarded = [];

    public $casts = [
        'value' => NumberFromDecimal::class,
    ];
}
