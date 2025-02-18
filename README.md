# Number

[![Latest Version on Packagist](https://img.shields.io/packagist/v/worksome/number.svg?style=flat-square&label=Packagist)](https://packagist.org/packages/worksome/number)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/worksome/number/run-tests.yml?branch=main&label=Tests&style=flat-square)](https://github.com/worksome/number/actions?query=workflow%3ATests)
[![GitHub PHPStan Action Status](https://img.shields.io/github/actions/workflow/status/worksome/number/phpstan.yml?branch=main&label=PHPStan&style=flat-square)](https://github.com/worksome/number/actions?query=workflow%3APHPStan)
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
