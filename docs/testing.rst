=======
Testing
=======

Current Build Status:

.. image:: https://travis-ci.org/bitpay/php-bitpay-client.svg?branch=master
    :target: https://travis-ci.org/bitpay/php-bitpay-client

Testing ensures that code is stable and that the code quality is high by holding
every person to the same standards. Before you start, please make sure you have
install the required development dependencies.

.. code-block:: bash

    composer.phar install
    npm install

.. note::

    By default Composer will install the development packages. You should
    not need to run ``install`` or ``update``.

.. note::

    node is used for PhantomJS and a few other tools that help out with the
    required testing to make sure that the code is able to talk to BitPay servers
    and should give us an early warning if things update or change without notice.

PHPUnit
=======

To run phpunit, use the command:

.. code-block:: bash

    php bin/phpunit -c build/

This will run the tests and will also generate the code coverage reports which
you can view by opening the HTML files generated in the ``build/docs/code-coverage``.

You can also run phpunit on just one file, for example:

.. code-block:: bash

    php bin/phpunit -c build/ tests/Bitpay/PrivateKeyTest.php

Running tests on just one file can be helpful since you are not running the
entire test suite.

.. note::

    You can run `unit` tests or `integration` tests by providing the testsuite
    command line option like so:

        php bin/phpunit -c build/ --testsuite unit
        php bin/phpunit -c build/ --testsuite integration

    Also, please be aware that to have integration tests work, you will need to
    have PhantomJS running, see `Makefile` for getting it setup or just run
    `make phantomjs` in a terminal window.

PHP_CodeSniffer
===============

Please make sure your code is PSR1 and PSR2 compliant. To do this, ``phpcs``
has been include in the require-dev section of composer and also is part of
the build process on Travis CI.

To make sure your code is compliant, please run the command:

.. code-block:: bash

    php bin/phpcs -n --standard=PSR1,PSR2 --report=full src/

Please fix any violations that are found.

Tools
-----

Some helpful tools that you can use include

* `php-cs-fixer <https://github.com/fabpot/PHP-CS-Fixer>`_

PHP Mess Detector
=================

To make sure that code conforms to standards and does not become too jacked up,
please try to use phpmd. You can see the rules in ``build/rulesets/phpmd.xml``.

.. code-block:: bash

    php bin/phpmd src/ text build/rulesets/phpmd.xml

PHPbrew
=======

In order to test that a math library is isused when the others aren't installed 
phpbrew can be used to install bcmath and gmp or uninstall either of them.

.. code-block:: bash

    ./tests/Bitpay/testMaths

.. note::
    
    phpbrew is required for testMaths to run properly.