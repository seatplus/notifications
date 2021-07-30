# Seatplus/notifications

[![Latest Version on Packagist](https://img.shields.io/packagist/v/seatplus/notifications.svg?style=flat-square)](https://packagist.org/packages/seatplus/notifications)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/seatplus/notifications/run-tests?label=tests)](https://github.com/seatplus/notifications/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/seatplus/notifications/Check%20&%20fix%20styling?label=code%20style)](https://github.com/seatplus/notifications/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/seatplus/notifications.svg?style=flat-square)](https://packagist.org/packages/seatplus/notifications)

This package serves to be the base notification package for any driver implementation. It offers a way to subscripe to notifications as well as an outbox which may be worked on by custom worker instances.
## Installation

You can install the package via composer:

```bash
composer require seatplus/notifications
```

You can publish and run the migrations with:

```bash
php artisan migrate
```



## Usage


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

- [Herpaderp Aldent](https://github.com/herpaderpaldent)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
