##  Usage
Please make sure that you have Installed this library and have
Configured it correctly. This section will help guide you on how to use
this library.

Autoloader
==========

To use the library's autoloader (which doesn't include composer dependencies)
instead of composer's autoloader, use the following code:

``` {.sourceCode .php}
<?php
$autoloader = __DIR__ . '/relative/path/to/Bitpay/Autoloader.php';
if (true === file_exists($autoloader) &&
    true === is_readable($autoloader))
{
    require_once $autoloader;
    \Bitpay\Autoloader::register();
} else {
    throw new Exception('BitPay Library could not be loaded');
}
```

Dependency Injection
====================

This library relies heavily on what is known as [Dependency
Injection](http://en.wikipedia.org/wiki/Dependency_injection) and this
is helped using the `Bitpay\Bitpay` class and the [Dependency Injection
Component](http://symfony.com/doc/current/components/dependency_injection/index.html)
provided by [Symfony](http://symfony.com/). It might be helpful to read
a little on these.

Services
========

Inside the container there are a few services that will help you develop
your application to work with BitPay. For example, the `client` service
allows you to make requests to our API and receive responses back
without having to do too much work.

You can see a list of services you have access to by checking out the
[services.xml](https://github.com/bitpay/php-bitpay-client/blob/master/src/Bitpay/DependencyInjection/services.xml)
file.

To gain access to any of these services, you first need to instantiate
the `Bitpay` class with your configuration options.

``` {.sourceCode .php}
$bitpay = \Bitpay\Bitpay($configuration);
```

> **note**
>
> `configuration` is either the path to a yaml file or an array of
> configuration options.

Sending your own Requests
=========================

You can easily send your own requests to BitPay's API with a little
work. For all the requests you can make, please see the [API
Documentation](https://bitpay.com/api) on the website.

To get started you need to create your
[Request](https://github.com/bitpay/php-bitpay-client/blob/master/src/Bitpay/Client/Request.php)

``` {.sourceCode .php}
$request = new \Bitpay\Client\Request();
```

This is the object that you will pass to the Client.

``` {.sourceCode .php}
$request->setHost('https://bitpay.com');
$request->setMethod(Request::METHOD_GET);
$request->setPath('/invoices/InvoiceIdHere');

$client = $bitpay->get('client');

// @var Bitpay\Client\ResponseInterface
$response = $client->sendRequest($request);
```

That's all there is to it. Just make your Request object and have the
Client send it. You'll get a
[Response](https://github.com/bitpay/php-bitpay-client/blob/master/src/Bitpay/Client/ResponseInterface.php)
object in return which you can use to do whatever it is you need to do.
