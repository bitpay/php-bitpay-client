<?php
/**
 * Copyright (c) 2014-2015 BitPay
 *
 * 004 - Advanced Functionality
 *
 * Requirements:
 *   - Account on https://test.bitpay.com
 *   - Baisic PHP Knowledge
 *   - Private and Public keys from 001.php
 *   - Token value obtained from 002.php
 *   - Able to complete 003.php
 */
require __DIR__.'/../../vendor/autoload.php';

/**
 * This example file will teach you how to make an invoice using a container.
 * The container contains all the objects that you would need along with a few
 * configuration options that should allow you to easily configure and change
 * based on your environment or server.
 *
 * Below are some variables that may or may not be needed based on what you
 * comment/uncomment.
 */
$pairingCode    = 'YCBrKpr';
$tokenString    = 'realtokengoeshere';
$privateKeyPath = '/tmp/bitpay.pri';
$publicKeyPath  = '/tmp/bitpay.pub';
$keyStoragePassword = 'YourTopSecretPassword';
/*** end options ***/

/**
 * The Bitpay class takes care of all the dependency injection for
 * you while at the same time allowing you to easily configure based on
 * environment variables or other configuration system you have.
 *
 * For a list of options you can pass in please see the Bitpay\Config\Configuration
 * class. You will find a list of options and the default and valid values.
 */
$bitpay = new \Bitpay\Bitpay(
    array(
        'bitpay' => array(
            'network'     => 'testnet', // Valid values are testnet/livenet
            'public_key'  => $publicKeyPath,
            'private_key' => $privateKeyPath,
            'key_storage_password' => $keyStoragePassword,
        )
    )
);
echo 'Bitpay class initialized.'.PHP_EOL;

/**
 * If you have not already generated and persisted you keys, please uncomment
 * this code. This should only be ran once. Once you have generated your key
 * pairs, keep you keys in a secure location. If you regenerate your keys, you
 * will need to repair and get a new token.
 *
$privateKey = \Bitpay\PrivateKey::create($privateKeyPath)->generate();
$publicKey  = \Bitpay\PublicKey::create($publicKeyPath)->setPrivateKey($privateKey)->generate();
$bitpay->get('key_manager')->persist($privateKey);
$bitpay->get('key_manager')->persist($publicKey);
echo 'Public and Private keys have been generated and persisted.'.PHP_EOL;
//exit(0); // exit in case you just wanted to generate keys
 */

/**
 * If you have not already obtained a token, please uncomment this code, you
 * will need a pairing code. A pairing code is a one time use code and can only
 * be used once.
 *
$publicKey = $bitpay->get('public_key'); // @var \Bitpay\PublicKey
$sin       = \Bitpay\SinKey::create()->setPublicKey($publicKey)->generate(); // @var \Bitpay\SinKey
if (empty($pairingCode)) { throw new \Exception('Please set a pairing code to a value.'); }
$token     = $bitpay->get('client')->createToken(
    array(
        'pairingCode' => $pairingCode,
        'label'       => 'Tutorial 004',
        'id'          => (string) $sin,
    )
);
echo 'Token Obtained "'.$token->getToken().'"'.PHP_EOL;
//exit(0);
 */

/**
 * If you already have a token, uncomment this code
 *
 */
$token = new \Bitpay\Token();
$token->setToken($tokenString);

/**
 * Code that makes the invoice
 */
$invoice = new \Bitpay\Invoice();
$item    = new \Bitpay\Item();
$item
    ->setCode('skuNumber')
    ->setDescription('General Description of Item')
    ->setPrice('1.99');
$invoice->setCurrency(new \Bitpay\Currency('USD'));
$invoice->setItem($item);
$client = $bitpay->get('client');
$client->setToken($token);
try {
    $client->createInvoice($invoice);
} catch (\Exception $e) {
    $request  = $client->getRequest();
    $response = $client->getResponse();
    echo (string) $request.PHP_EOL.PHP_EOL.PHP_EOL;
    echo (string) $response.PHP_EOL.PHP_EOL;
    exit(1); // We do not want to continue if something went wrong
}

echo 'Invoice "'.$invoice->getId().'" created, see '.$invoice->getUrl().PHP_EOL;

/**
 * Q: PHP Fatal error:  Uncaught exception 'Exception' with message 'Unable to create token'
 * A: This error will happen if you are are trying to use a pairing code that has already
 *    been used. To fix this issue, create a new pairing code and use that one.
 *
 *    It might also mean that the pairing code you obtained was generated on 'bitpay.com'
 *    and you have the network setup for 'testnet' or you generated a pairing code on
 *    'test.bitpay.com' and have the network set to 'livenet'. Please double check your
 *    configuration settings.
 *
 * Q: PHP Fatal error:  Uncaught exception 'Exception' with message 'Could not decode key'
 * A: The key was found, however when trying to decrypt it, it was unabled because the key
 *    is either not encrypted or the key is encrypted and the password you have to decrypt
 *    it is invalid. Double check and make sure that the settings are correct, if unsure,
 *    regenerate your keys. You will need to decrypt using the same storage engine.
 */
