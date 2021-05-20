# Kapital Bank Payment Gateway for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/sharifelvin/laravel-kapitalbank.svg?style=flat-square)](https://packagist.org/packages/sharifelvin/laravel-kapitalbank)
[![Total Downloads](https://img.shields.io/packagist/dt/sharifelvin/laravel-kapitalbank.svg?style=flat-square)](https://packagist.org/packages/sharifelvin/laravel-kapitalbank)

Kapital Bank Transfer is a laravel package for Azerbaijan's Kapital Bank Payment gateway. 

## Installation

You can install the package via composer:

```bash
composer require sharifelvin/laravel-kapitalbank
```

## Usage

``` php

$request = [
    'lang' => 'RU',
    'amount' => '1000'
    'currency' => '922',
    'description' => 'A sample descriotion...',
    'approve' => 'http://example.com/approve',
    'cancel' => 'http://example.com/cancel',
    'decline' => 'http://example.com/decline',
];

KapitalBankTransferFacade::load()->create($request);

```

### Testing

``` bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email sharifelvin@gmail.com instead of using the issue tracker.

## Credits

- [Elvin Sharifov](https://github.com/sharifelvin)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.