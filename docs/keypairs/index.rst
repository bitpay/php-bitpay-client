========
Keypairs
========

All keys implement the KeyInterface and MUST be able to be serialized and
unserialized.

Generating Keypairs
===================

Private
-------

.. code-block:: php

    $priv = new \Bitpay\PrivateKey();
    $priv->generate();

Public
------

.. code-block:: php

    $pub = new \Bitpay\PublicKey();
    $pub->setPrivateKey($priv);
    $pub->generate();

SIN
---

.. code-block:: php

    $sin = new \Bitpay\SinKey();
    $sin->setPublicKey($pub);
    $sin->generate();

Storing Keys
============

Keys can be stored using the filesystem or you can easily make your own storage
service.

Filesystem
----------

Keys can be store on the filesystem using the following code. Keys MUST have
already been generated.

.. code-block:: php

    $privateKey = new \Bitpay\PrivateKey('/path/to/private.key');
    $privateKey->generate();

    $keyManager = new \Bitpay\KeyManager(new \Bitpay\Storage\FilesystemStorage());
    $keyManager->persist($privateKey);

The key can now be found at `/path/to/private.key`. Loading the key is just as
easy.

.. code-block:: php

    $key = $manager->load('/path/to/private.key');

Encrypted on Filesystem
-----------------------

.. code-block:: php

    $privateKey = new \Bitpay\PrivateKey('/path/to/private.key');
    $privateKey->generate();

    $password   = 'satoshi';
    $keyManager = new \Bitpay\KeyManager(new \Bitpay\Storage\EncryptedFilesystemStorage($password));
    $keyManager->persist($privateKey);

Cookbook
========

.. toctree::
    :maxdepth: 1

    storage
