# BitPay Library for PHP [![](https://secure.travis-ci.org/fedeisas/php-client.png)](http://travis-ci.org/fedeisas/php-client)
Powerful, flexible, lightweight interface to the BitPay Bitcoin Payment Gateway API.

## Installing via Composer

The recommended way to install the BitPay PHP Client is through [Composer](http://getcomposer.org).
```bash
# Install Composer
$ curl -sS https://getcomposer.org/installer | php

# Add BitPay as a dependency
$ php composer.phar require bitpay/php-client
```

After installing, you need to require Composer's autoloader:
```php
require 'vendor/autoload.php';
```
The library requires **PHP 5.3+ and is PSR-0 compatible**.

## Basic Usage

To create an invoice:
```php
<?php

require 'vendor/autoload.php';

$bitPay = new BitPay\BitPay(
  new BitPay\Request\Curl,
  new BitPay\Hash,
  'API-KEY',
  $options // array, optional
);

$invoice = $bitPay->createInvoice($orderID, $price); // returns Invoice Object
```

With invoice creation, `orderID` and `currency` are the only required fields. If you are sending a customer from your website to make a purchase, setting `redirectURL` on BitPay constructor options is required.

Response will be an object with information on your newly created invoice. Send your customer to the `url` to complete payment:
```php
class stdClass#5 (10) {
  public $id =>
  string(10) "EXAMPLE-ID"
  public $url =>
  string(40) "https://bitpay.com/invoice?id=EXAMPLE-ID"
  public $posData =>
  string(67) "{"posData":[],"hash":"HASH"}"
  public $status =>
  string(3) "new"
  public $btcPrice =>
  string(6) "1.0000"
  public $price =>
  int(1)
  public $currency =>
  string(3) "BTC"
  public $invoiceTime =>
  int(1386958726781)
  public $expirationTime =>
  int(1386959626781)
  public $currentTime =>
  int(1386958726861)
}
```
There are many options available when creating invoices, which are listed in the [BitPay API documentation](https://bitpay.com/bitcoin-payment-gateway-api).

To get updated information on this invoice, make a get call with the ID returned:
```php
<?php

require 'vendor/autoload.php';

$bitPay = new BitPay\BitPay(
  new BitPay\Request\Curl,
  new BitPay\Hash,
  'API-KEY',
  $options // array, optional
);

$invoice = $bitPay->createInvoice($orderID); // returns Invoice Object
```

## Options

When you instantiate BitPay you can pass options as a fourth argument. You can also set this options dinamically with `setOptions()`.
Please look carefully through these options and adjust according to your installation.

| Option | Default | Description |
|--------|:-------:|-------------|
| verifyPost | `true` | Whether to verify POS data by hashing above api key.  If set to false, you should have some way of verifying that callback data comes from bitpay.com. |
| notificationEmail | `''` | Email where invoice update notifications should be sent. |
| notificationURL | `''` | URL where bit-pay server should send update notifications. See API doc for more details. |
| redirectURL | `''` | URL where the customer should be directed to after paying for the order. |
| currency | `'BTC'` | This is the currency used for the price setting.  A list of other pricing currencies supported is found at bitpay.com |
| physical | `true` | Indicates whether anything is to be shipped with the order (if false, the buyer will be informed that nothing is to be shipped) |
| fullNotifications | `true` | If set to false, then notificaitions are only sent when an invoice is confirmed (according the the transactionSpeed setting). If set to true, then a notification will be sent on every status change. |
| transactionSpeed | `'low'` | Transaction speed: low/medium/high. See API docs for more details. |

## API Documentation

API Documentation is available on the [BitPay site](https://bitpay.com/bitcoin-payment-gateway-api).

## Running the Tests
```bash
$ composer install --dev
$ ./vendor/bin/phpunit
```
In addition to a full test suite, there is Travis integration.

## Found a bug?
Let us know! Send a pull request or a patch. Questions? Ask! We're here to help. We will respond to all filed issues.

## Authors
* Fede Isas