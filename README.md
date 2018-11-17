<p align="center"><img src="./Larapay.png"></p>

<p align="center">
    <a href="./LICENSE.md">
        <img src="https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square" alt="Software License">
    </a>
    <a href="https://scrutinizer-ci.com/g/nxmad/Larapay/?branch=master">
        <img src="https://scrutinizer-ci.com/g/nxmad/Larapay/badges/quality-score.png?b=master" alt="Scrutinizer Code Quality">
    </a>
    <a href="https://scrutinizer-ci.com/g/nxmad/Larapay/build-status/master">
        <img src="https://scrutinizer-ci.com/g/nxmad/Larapay/badges/build.png?b=master" alt="Build Status">
    </a>
</p>

Installation
-------
- Require it using composer
    ``` bash
    $ composer require nxmad/larapay
    ```

- Add the Service Provider to your `config/app.php` providers array (you can skip this step for Laravel 5.4+)
   ``` php
   Skylex\Larapay\LarapayServiceProvider::class,
   ```

- Publish default configuration file `larapay.php`
   ``` php
   $ php artisan vendor:publish
   ```

Usage guide
-----------
Please see [Wiki](/wiki).

Testing
-------
*Since 1.0*
``` bash
$ composer test
```

Credits
-------
- [Alex Balatsky](https://github.com/nxmad)
- [All Contributors](../../contributors)

License
-------
The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

