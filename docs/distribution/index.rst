============
Distribution
============

This library comes with the ability to create a phar file that includes all the
BitPay classes along with the required vendor classes.

Building the Distribution
=========================

.. code-block:: bash

    php bin/robo builddist

The file can now be found in the build/dist directory.

Using the Distribution
======================

In your code
------------

.. code-block:: php

    require 'phar:///full/path/to/bitpay.phar/src/Bitpay/Autoloader.php'
    \Bitpay\Autoloader::register();

On the Command Line
-------------------

.. code-block:: bash

    php /path/to/bitpay.phar

FAQs
====

PHP Warning: proc_open(): unable to create pipe Too many open files...
----------------------------------------------------------------------

Increase the amount of files that can be opened.

.. code-block:: bash

    ulimit -Sn 1024
