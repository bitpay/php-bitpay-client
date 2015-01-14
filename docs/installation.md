##  Installation
Composer
========

Add the following to your composer.json

``` {.sourceCode .json}
"require": {
    "bitpay/php-client": "~2.0"
}
```

> **note**
>
> To obtain the LATEST development code which is on the master branch
> you can either update your
> [minimum-stability](https://getcomposer.org/doc/04-schema.md#minimum-stability)
> setting or put the version as `~2.0@dev`

Once you have added this to your composer.json file you will need to
install the library in your project.

``` {.sourceCode .bash}
php composer.phar update bitpay/php-client
```

Composer Alternative
--------------------

By running this composer command, you will add the library to
composer.json and install the latest version.

``` {.sourceCode .bash}
php composer.phar require "bitpay/php-client ~2.0"
```

Setup
=====

Before you begin to use the API, you will need to create public/private
keys and pair them to your account. The command line tool was made to
help you generate your keys and to pair them. Before you can make any
request that is not open to the public, you will need to do this.

> **note**
>
> You will need to create and pair your keys before you can start
> creating invoices and doing other things with the API that are not
> open to the public.
