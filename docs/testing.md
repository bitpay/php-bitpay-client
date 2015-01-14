##  Testing
Current Build Status:

[![image](https://travis-ci.org/bitpay/php-bitpay-client.svg?branch=master)](https://travis-ci.org/bitpay/php-bitpay-client)

Testing ensures that code is stable and that the code quality is high by
holding every person to the same standards. Before you start, please
make sure you have install the required development dependencies.

``` {.sourceCode .bash}
composer.phar install
npm install
```

> **note**
>
> By default Composer will install the development packages. You should
> not need to run `install` or `update`.

> **note**
>
> node is used for PhantomJS and a few other tools that help out with
> the required testing to make sure that the code is able to talk to
> BitPay servers and should give us an early warning if things update or
> change without notice.

PHPUnit
=======

To run phpunit, use the command:

``` {.sourceCode .bash}
php bin/phpunit -c build/
```

This will run the tests and will also generate the code coverage reports
which you can view by opening the HTML files generated in the
`build/docs/code-coverage`.

You can also run phpunit on just one file, for example:

``` {.sourceCode .bash}
php bin/phpunit -c build/ tests/Bitpay/PrivateKeyTest.php
```

Running tests on just one file can be helpful since you are not running
the entire test suite.

> **note**
>
> You can run unit tests or integration tests by providing the testsuite
> command line option like so:
>
> > php bin/phpunit -c build/ --testsuite unit php bin/phpunit -c
> > build/ --testsuite integration
>
> Also, please be aware that to have integration tests work, you will
> need to have PhantomJS running, see Makefile for getting it setup or
> just run make phantomjs in a terminal window.

PHP\_CodeSniffer
================

Please make sure your code is PSR1 and PSR2 compliant. To do this,
`phpcs` has been include in the require-dev section of composer and also
is part of the build process on Travis CI.

To make sure your code is compliant, please run the command:

``` {.sourceCode .bash}
php bin/phpcs -n --standard=PSR1,PSR2 --report=full src/
```

Please fix any violations that are found.

Tools
-----

Some helpful tools that you can use include

-   [php-cs-fixer](https://github.com/fabpot/PHP-CS-Fixer)

PHP Mess Detector
=================

To make sure that code conforms to standards and does not become too
jacked up, please try to use phpmd. You can see the rules in
`build/rulesets/phpmd.xml`.

``` {.sourceCode .bash}
php bin/phpmd src/ text build/rulesets/phpmd.xml
```

Mink/Behat
==========

This will run tests verifying that the client is able to perform it's
core functionality.

To run mink/behat integration tests, use the command:

``` {.sourceCode .bash}
source ./integration_tests.sh
```

URL, email, and password can be passed in as arguments to
integration\_tests.sh like so:

``` {.sourceCode .}
source ./integration_tests.sh 'https://bobert.bp:8090' bobert@gmail.com 'abc123%^&@ac'
```

You can configure which instance of bitpay.com this will test with to by
changing the url in the behat.yml file. Make sure you replace username
and password with the credentials used to log into the bitpay site you
are testing with.

You can also run specific tests from a command like so:

``` {.sourceCode .bash}
php bin/behat tests/integrations/invoice_create.feature
```

And you can run specific lines from these tests by:

``` {.sourceCode .bash}
php bin/behat tests/integrations/invoice_create.feature:20
```

> **note**
>
> Tests run individually require you to set environment variables for
> your bitpay credentials or they must be set in the behat.yml file.
>
> Also keep in mind that rate limiters may hinder some tests and they
> need to be reset every so often.

> **note**
>
> pairing.feature's test "the client has a bad port configuration to an
> incorrect port" requires ports that vary from computer to computer so
> you may need to manually change these in order to avoid error.
>
> A timing issue will occasionally occur saying: "Notice: Undefined
> variable: response in tests/integrations/bootstrap/FeatureContext.php
> line 368" and there will be a message from BitPay saying: "This
> endpoint does not support the public facade" This is likely a timing
> issue and the tests will likely pass when run again.
