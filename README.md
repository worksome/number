# Number

[![Latest Version on Packagist](https://img.shields.io/packagist/v/worksome/number.svg?style=flat-square)](https://packagist.org/packages/worksome/number)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/worksome/number/run-tests?label=tests)](https://github.com/worksome/number/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/worksome/number/Check%20&%20fix%20styling?label=code%20style)](https://github.com/worksome/number/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/worksome/number.svg?style=flat-square)](https://packagist.org/packages/worksome/number)

This is where your description should go. Limit it to a paragraph or two. Consider adding a small example.

## Installation

You can install the package via composer:

```bash
composer require worksome/number
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="number-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="number-config"
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="number-views"
```

## Usage

```php
$number = new Worksome\Number();
echo $number->echoPhrase('Hello, Worksome!');
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Owen Voke](https://github.com/owenvoke)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
