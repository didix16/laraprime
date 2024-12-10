# An open source admin panel like Laravel Nova but using React + PrimeReact as frontend 

[![Latest Version on Packagist](https://img.shields.io/packagist/v/didix16/laraprime.svg?style=flat-square)](https://packagist.org/packages/didix16/laraprime)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/didix16/laraprime/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/didix16/laraprime/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/didix16/laraprime/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/didix16/laraprime/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/didix16/laraprime.svg?style=flat-square)](https://packagist.org/packages/didix16/laraprime)

An open source admin panel like Laravel Nova but using React + PrimeReact as frontend

## Installation

You can install the package via composer:

```bash
composer require didix16/laraprime
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="laraprime-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="laraprime-config"
```

This is the contents of the published config file:

```php
return [
];
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="laraprime-views"
```

## Usage

```php
$laraprime = new Didix16\LaraPrime();
echo $laraprime->echoPhrase('Hello, Didix16!');
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Dídac Rodríguez](https://github.com/didix16)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
