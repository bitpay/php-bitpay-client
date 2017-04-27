##  Keypairs
All keys implement the KeyInterface and MUST be able to be serialized
and unserialized.

Generating Keypairs
===================

Private
-------

``` {.sourceCode .php}
$priv = new \Bitpay\PrivateKey();
$priv->generate();
```

Public
------

``` {.sourceCode .php}
$pub = new \Bitpay\PublicKey();
$pub->setPrivateKey($priv);
$pub->generate();
```

SIN
---

``` {.sourceCode .php}
$sin = new \Bitpay\SinKey();
$sin->setPublicKey($pub);
$sin->generate();
```

Storing Keys
============

Keys can be stored using the filesystem or you can easily make your own
storage service.

Filesystem
----------

Keys can be store on the filesystem using the following code. Keys MUST
have already been generated.

``` {.sourceCode .php}
$privateKey = new \Bitpay\PrivateKey('/path/to/private.key');
$privateKey->generate();

$keyManager = new \Bitpay\KeyManager(new \Bitpay\Storage\FilesystemStorage());
$keyManager->persist($privateKey);
```

The key can now be found at /path/to/private.key. Loading the key is
just as easy.

``` {.sourceCode .php}
$key = $manager->load('/path/to/private.key');
```

Encrypted on Filesystem
-----------------------

``` {.sourceCode .php}
$privateKey = new \Bitpay\PrivateKey('/path/to/private.key');
$privateKey->generate();

$password   = 'satoshi';
$keyManager = new \Bitpay\KeyManager(new \Bitpay\Storage\EncryptedFilesystemStorage($password));
$keyManager->persist($privateKey);
```

Cookbook
========
* [Creating your own storage clas](https://github.com/bitpay/php-bitpay-client/blob/master/docs/keypairs/storage.md)
