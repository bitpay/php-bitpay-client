============
Installation
============

Composer
========

Add the following to your composer.json

.. code-block:: json

    "require": {
        "bitpay/php-client": "~2.0"
    }

Once you have added this to your composer.json file you will need to install
the library in your project.

.. code-block:: bash

    php composer.phar update bitpay/php-client

Composer Alternative
--------------------

By running this composer command, you will add the library to composer.json
and install the latest version.

.. code-block:: bash

    php composer.phar require "bitpay/php-client ~2.0"

Setup
=====

Before you begin to use the API, you will need to create public/private keys
and pair them to your account. The command line tool was made to help you
generate your keys and to pair them. Before you can make any request that is
not open to the public, you will need to do this.
