Configuration
-------------

Configuration of this library can be done two different ways: using a PHP array or by using a YML file.

Config Options
--------------

All configuration options can be found in the class `BitpayConfigConfiguration`:

    public\_key

This is the full path and name for the public key. The default value is `$HOME/.bitpay/bitpay.pub`

    private\_key

This is the full path and name for the private key.  The default value is `$HOME/.bitpay/bitpay.key`

    network

Specifies using the Live Bitcoin network or the Test Bitcoin network: `livenet` or `testnet`.  The default is `livenet`.

    adapter

Used mostly for testing. You shouldn't need to change or update this value.

    key\_storage

The `key_storage` option allows you to specify a class for persisting and retrieving keys.  By default this uses the `Bitpay\Storage\EncryptedFilesystemStorage` class.

    key\_storage\_password

This is the password used to encrypt and decrypt keys on the filesystem.

Example YAML config
-------------------------

```yaml
# /path/to/config.yml
bitpay:
    network: testnet
```

Corresponding PHP code:
```php
$bitpay = new \Bitpay\Bitpay('/path/to/config.yml');
```

Example array config
------------------------

```php
$bitpay = new \Bitpay\Bitpay(
    array(
        'bitpay' => array(
            'network' => 'testnet',
        )
    )
);
```
