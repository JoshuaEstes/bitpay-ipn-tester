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
