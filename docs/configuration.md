##  Configuration
Configuration can of this library can be done in various different ways.
Out of the box, you are able to configure the client using a PHP array
or by using a yml file.

Config Options
==============

All configuration options can be found in the class
BitpayConfigConfiguration

public\_key
-----------

This is the full path to the public key and the name. The default for
this is `$HOME/.bitpay/bitpay.pub`

private\_key
------------

Default for this is `$HOME/.bitpay/bitpay.key`

network
-------

Use livenet or testnet. Valid values are `testnet` or `livenet` and it
defaults to `livenet`.

adapter
-------

Used mostly for testing. You shouldn't need to change or update this.

key\_storage
------------

The `key_storage` option allows you to give a class for persisting keys
and loading them. The class must also implement
`Bitpay\Storage\StorageInterface`.

By default this uses the `Bitpay\Storage\EncryptedFilesystemStorage`.

key\_storage\_password
----------------------

This is the password used to encrypt and decrypt keys on the filesystem.
If you have generated keys and change this to something else, you will
be unable to load your keys.

Example YAML config
===================

``` {.sourceCode .yaml}
# /path/to/config.yml
bitpay:
    network: testnet
```

``` {.sourceCode .php}
$bitpay = new \Bitpay\Bitpay('/path/to/config.yml');
```

Example array config
====================

``` {.sourceCode .php}
$bitpay = new \Bitpay\Bitpay(
    array(
        'bitpay' => array(
            'network' => 'testnet',
        )
    )
);
```
