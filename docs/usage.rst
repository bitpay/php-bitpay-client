=====
Usage
=====

Please make sure that you have Installed this library and have Configured it
correctly. This section will help guide you on how to use this library.

This library relies heavily on what is known as `Dependency Injection <http://en.wikipedia.org/wiki/Dependency_injection>`_
and this is helped using the ``Bitpay\Bitpay`` class and the `Dependency Injection Component <http://symfony.com/doc/current/components/dependency_injection/index.html>`_
provided by `Symfony <http://symfony.com/>`_. It might be helpful to read a little on these.

Services
========

Inside the container there are a few services that will help you develop your
application to work with BitPay. For example, the ``client`` service allows
you to make requests to our API and receive responses back without having to
do too much work.

You can see a list of services you have access to by checking out the
`services.xml <https://github.com/bitpay/php-bitpay-client/blob/master/src/Bitpay/DependencyInjection/services.xml>`_
file.

To gain access to any of these services, you first need to instantiate the ``Bitpay`` class with
your configuration options.

.. code-block:: php

    $bitpay = \Bitpay\Bitpay($configuration);

.. note::

    ``configuration`` is either the path to a yaml file or an array of configuration
    options.
