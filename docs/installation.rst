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

Detailed Setup
--------------

.. note::

    This assumes that you are included this in a project and have not changed the
    default `bin-dir` which is located in `vendor/bin`.

You will first need to generate your public and private keys.

.. code-block:: bash

    php vendor/bin/bitpay keygen

.. note::

    The keys generated are store as api.key and api.pub. The output will let
    you know the directory they are saved in. Also be aware that generating
    the keys will chmod them to the correct modes.

Next you will need to visit https://bitpay.com/api-tokens and click the button
that says "Add New Token". This will generate a pairing code for you to use.

Now head back to the command line tool with your pairing code and run.

.. code-block:: bash

    php vendor/bin/bitpay pair PairingCode

Once you have done this you are now able to use the command line tool to make
more advanced requests to the BitPay API for things such as creating invoices.

.. note::
    
    You can read more about the console tool by viewing the section about it
    in this documentation.
