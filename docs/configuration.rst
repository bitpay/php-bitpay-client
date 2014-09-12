=============
Configuration
=============

Configuration can of this library can be done in various different ways. Out of
the box, you are able to configure the client using a PHP array or by using
a yml file.


Config Options
==============

public_key_filename
-------------------

private_key_filename
--------------------

sin_filename
------------

network
-------

adapter
-------

logger_file
-----------

logger_level
------------

key_storage
-----------


Example YAML config
===================

.. code-block:: yaml

    # /path/to/config.yml
    bitpay:
        logger_file:  /var/logs/bitpay.log
        logger_level: 100

.. code-block:: php

    $bitpay = new \Bitpay\Bitpay('/path/to/config.yml');


Example array config
====================

.. code-block:: php

    $bitpay = new \Bitpay\Bitpay(
        array(
            'bitpay' => array(
                'logger_file' => '/var/logs/bitpay.log',
                'logger_level' => \Monolog\Logger::DEBUG
            )
        )
    );
