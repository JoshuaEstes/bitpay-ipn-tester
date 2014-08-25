joshuaestes/bitpay-ipn-tester
=============================

This repository is used to test IPNs.

# Installation

```bash
git clone https://github.com/JoshuaEstes/bitpay-ipn-tester.git
cd bitpay-ipn-tester
curl -s https://getcomposer.org/installer | php
composer.phar install
git remote add -f bitpay-php-client https://github.com/bitpay/php-client.git
vagrant up
```

http://127.0.0.1:8080/index_dev.php

# Configuration

## Testing incoming IPN

To test incoming IPNs from BitPay, edit `config/prod.php` and update the
notificationURL to what it should be. This can only be done if the server
this code is on is public. This can be down with ease using the `vagrant share`
command.

Once you run `vagrant share` the URL you are given is the one you want to use,
and please make sure that you add `/index_dev.php/ipn` onto the end so it will
hit the correct route.

You should also configure the `lib/bp_options.php` file with your API key and
enable testnet if you have an account at test.bitpay.com

# Using

Open your browser to be given a web interface that will send IPNs to the server
of your choice. These requests do not come from BitPay.

You can run `php bin/console invoice:create` to create an invoice on BitPay's
servers. If you have configured `bp_options.php` and have edited your
`notificationURL` setting in `config/prod.php` you will be sent an IPN when you
pay the invoice. This can be handy if you are using testnet to try out IPNs.

# BitPay PHP Client

## Add Remote

```bash
git remote add -f bitpay-php-client https://github.com/bitpay/php-client.git
```

## Updating

```bash
git fetch bitpay-php-client master
git subtree pull --prefix lib/ bitpay-php-client master --squash
```
