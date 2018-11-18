<img src="./Larapay.png" width="283px" height="49px">
<hr />

<blockquote>
    Larapay — a powerful Laravel extension with 2 core functionalities:
    1) abstract interface for any payment gateway;
    2) accounting system for your users.
</blockquote>

<p>
    <a href="./LICENSE.md">
        <img src="https://img.shields.io/badge/license-MIT-blue.svg" alt="Software License">
    </a>
    <a href="https://scrutinizer-ci.com/g/nxmad/Larapay/?branch=master">
        <img src="https://scrutinizer-ci.com/g/nxmad/Larapay/badges/quality-score.png?b=master" alt="Scrutinizer Code Quality">
    </a>
    <a href="https://scrutinizer-ci.com/g/nxmad/Larapay/build-status/master">
        <img src="https://scrutinizer-ci.com/g/nxmad/Larapay/badges/build.png?b=master" alt="Build Status">
    </a>
</p>

Installation
------------
This package can be installed as a [Composer](https://getcomposer.org/) dependency.
``` bash
$ composer require nxmad/larapay
```

If you don't use auto-discovery (or your Laravel version < 5.5), add the ServiceProvider to the `providers` array in `config/app.php`
``` php
Nxmad\Larapay\LarapayServiceProvider::class,
``` 

Publish default configuration file `larapay.php`
``` php
$ php artisan vendor:publish
```

Usage example
-------------
``` php
// Setup transaction for user
// Actually, you can use any Entity (Model) instead of User
// Transaction can have positive and negative amount
$transaction = $request->user()->setup(- $amount, $description);

// Check if the user can afford this order
if ($transaction->affordable() || $request->user()->canAfford($transaction)) {
    // do some logic...
    
    // and then save the transaction as sucessfull
    // this way:
    $transaction->makeSuccessful();
    
    // or this way:
    $transaction(Transaction::STATE_SUCCESSFUL);
} else {
    // Otherwise redirect user to the payment gateway (for .e.g)
    $gateway = payments('paypal');
    
    // There are 3 ways of interact with payment gateway:
    // Redirect (GET), POST form and any custom behavior you can define by yourself
    return $gateway->interact($transaction);
}
```
Please see [Wiki](../../wiki) for more examples.

Supported gateways
------------------
You can add your gateway implementation to this list by creating an [issue](../../issues/new).

| Gateway          | Composer package               | Maintainer                                |
|------------------|--------------------------------|-------------------------------------------|
| Unitpay          | nxmad/larapay-unitpay          | [Alex Balatsky](https://github.com/nxmad) |
| WebMoney         | nxmad/larapay-webmoney         | [Alex Balatsky](https://github.com/nxmad) |
| Qiwi.com P2P     | nxmad/larapay-qiwi-p2p         | [Alex Balatsky](https://github.com/nxmad) |
| Yandex.Money P2P | nxmad/larapay-yandex-money-p2p | [Alex Balatsky](https://github.com/nxmad) |

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
