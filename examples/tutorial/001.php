<?php

/**
 * 001 - Generate and Persist Keys
 *
 * Requirements:
 *   - Basic PHP knowledge
 */

// If you have not already done so, please run `composer.phar install`
require __DIR__.'/../../vendor/autoload.php';

/**
 * Start by creating a PrivateKey object
 */
$privateKey = new \Bitpay\PrivateKey();

// Generate a random number
$privateKey->generate();

// You can generate a private key with only one line of code like so
$privateKey = \Bitpay\PrivateKey::create()->generate();
// NOTE: This has overridden the previous $privateKey variable, although its
//       not an issue in this case since we have not used this key for
//       anything yet.

/**
 * Once we have a private key, a public key is created from it.
 */
$publicKey = new \Bitpay\PublicKey();

// Inject the private key into the public key
$publicKey->setPrivateKey($privateKey);

// Generate the public key
$publicKey->generate();

// NOTE: You can again do all of this with one line of code like so:
//       `$publicKey = \Bitpay\PublicKey::create()->setPrivateKey($privateKey)->generate();`

/**
 * Now that you have a private and public key generated, you will need to store
 * them somewhere. This optioin is up to you and how you store them is up to
 * you. Please be aware that you MUST store the private key with some type
 * of security. If the private key is comprimised you will need to repeat this
 * process.
 */

$storageEngine = new \Bitpay\Storage\EncryptedFilesystemStorage('YourTopSecretPassword');
