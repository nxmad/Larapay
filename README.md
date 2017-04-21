<p align="center"><img src="https://skylex.pro/uploads/larapay/logo.svg"></p>

<p align="center">
    <a href="./LICENSE.md">
        <img src="https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square" alt="Software License">
    </a>
    <a href="https://packagist.org/packages/laravel/socialite">
        <img src="https://img.shields.io/packagist/dt/balatsky/payments.svg?style=flat-square" alt="Total Downloads">
    </a>
    <a href="">
        <img src="https://img.shields.io/packagist/v/balatsky/payments.svg?style=flat-square" alt="Latest Version">
    </a>
    <a href="https://packagist.org/packages/laravel/socialite">
        <img src="https://styleci.io/repos/84937825/shield" alt="StyleCI">
    </a>
</p>

Install
-------
1. Install via Composer
    ``` bash
    $ composer require balatsky/larapay
    ```

2. Add the ServiceProvider to your `config/app.php` providers array:
   ``` php
   Skylex\Larapay\LarapayServiceProvider::class,
   ```

3. Publish default configuration file `payments.php`
   ``` php
   $ php artisan vendor:publish
   ```

Usage guide
-----------
- 🇷🇺 - [Русский](guide/ru.md)
- 🇺🇸 - Coming soon

Change log
----------
*Since 1.0*

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

Testing
-------
*Since 1.0*
``` bash
$ composer test
```

Credits
-------
- [Alex Balatsky](https://github.com/balatsky)
- [All Contributors](../../contributors)

License
-------
The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

