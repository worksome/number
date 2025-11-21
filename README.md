# Number

[![Latest Version on Packagist](https://img.shields.io/packagist/v/worksome/number.svg?style=flat-square&label=Packagist)](https://packagist.org/packages/worksome/number)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/worksome/number/tests.yml?branch=main&label=Tests&style=flat-square)](https://github.com/worksome/number/actions?query=workflow%3ATests)
[![GitHub PHPStan Action Status](https://img.shields.io/github/actions/workflow/status/worksome/number/static.yml?branch=main&label=Static%20Analysis&style=flat-square)](https://github.com/worksome/number/actions?query=workflow%3AStatic%20Analysis)
[![Total Downloads](https://img.shields.io/packagist/dt/worksome/number.svg?style=flat-square&label=Downloads)](https://packagist.org/packages/worksome/number)

A package for handling numbers in Laravel.

## Installation

You can install the package via composer:

```shell
composer require worksome/number
```

## Usage

```php
use Worksome\Number\Number;

$number = Number::of(100);

$number->mul(Number::of(5));

echo $number; // 500
```

## Casting
This package allows you to easily cast attributes on your Eloquent models to number types.

```php
use Illuminate\Database\Eloquent\Model;
use Worksome\Number\Casts\NumberFromCents;
use Worksome\Number\Casts\NumberFromDecimal;
use Worksome\Number\MonetaryAmount;
use Worksome\Number\Percentage;
use Worksome\Number\Number;

class Product extends Model
{
    protected $casts = [
        'a' => NumberFromCents::class,
        'b' => NumberFromDecimal::class,
        'c' => NumberFromDecimal::using(2, MonetaryAmount::class), // Cast to a specialised Number-class
        'd' => NumberFromDecimal::using(2, Percentage::class), // Cast to a specialised Number-class
        'e' => NumberFromDecimal::using(3), // Three decimal places - default is 2
    ];
}
```

## Available Number Types
The following Number types are available out of the box:
- `Number` - The base number type, with two decimal places. Can be configured to use a different default scale.
- `MonetaryAmount` - A number type for handling monetary amounts. Always uses two decimal places. Rounds automatically in all operations.
- `Percentage` - A number type for handling percentages. Makes it clear that the number represents a percentage, not an amount. Adds % on to string representations.

### GraphQL

This package also provides GraphQL scalar types for the [WebOnyx GraphQL PHP package](https://github.com/webonyx/graphql-php) / [Lighthouse](https://lighthouse-php.com).

These will be auto-registered by [`Worksome\Number\Providers\NumberServiceProvider`](src/Providers/NumberServiceProvider.php), however if you want to do this manually, they can be registered in the type registry using:

```php
// In Lighthouse (https://lighthouse-php.com)
$typeRegistry->register(new \Worksome\Number\GraphQL\Scalars\DecimalTwoType());
$typeRegistry->register(new \Worksome\Number\GraphQL\Scalars\PercentageType());
$typeRegistry->register(new \Worksome\Number\GraphQL\Scalars\StrictPercentageType());
```

## Testing

```shell
composer test
```

### Updating snapshots

To update Pest snapshots, run the following:

```shell
vendor/bin/pest -d --update-snapshots
```

## Changelog

Please see [GitHub Releases](https://github.com/worksome/number/releases) for more information on what has changed recently.

## Credits

- [Owen Voke](https://github.com/owenvoke)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
