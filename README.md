# Number

[![Latest Version on Packagist](https://img.shields.io/packagist/v/worksome/number.svg?style=flat-square&label=Packagist)](https://packagist.org/packages/worksome/number)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/worksome/number/Tests?label=Tests&style=flat-square)](https://github.com/owenvoke/number/actions?query=workflow%3ATests)
[![GitHub PHPStan Action Status](https://img.shields.io/github/workflow/status/worksome/number/PHPStan?label=PHPStan&style=flat-square)](https://github.com/owenvoke/number/actions?query=workflow%3APHPStan)
[![Total Downloads](https://img.shields.io/packagist/dt/worksome/number.svg?style=flat-square&label=Downloads)](https://packagist.org/packages/worksome/number)

A package for handling numbers in Laravel.

## Installation

You can install the package via composer:

```shell
composer require worksome/number
```

You can publish the config file with:

```shell
php artisan vendor:publish --tag="number-config"
```

## Usage

```php
use Worksome\Number\Number;

$number = Number::of(100);

$number->mul(Number::of(5));

echo $number; // 500
```

## Testing

```shell
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Credits

- [Owen Voke](https://github.com/owenvoke)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
