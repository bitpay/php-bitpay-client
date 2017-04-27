<?php
/**
 * Copyright (c) 2014-2015 BitPay
 */

/**
 * WARNING - This example will NOT work until you have generated your public
 * and private keys. Please see the example documentation on generating your
 * keys and also see the documentation on how to save those keys.
 *
 * Also please be aware that you CANNOT create an invoice until you have paired
 * the keys and received a token back. The token is usesd with the request.
 */

require __DIR__ . '/../vendor/autoload.php';


/**
 * Create a new client. You can see the example of how to configure this using
 * a yml file as well.
 */
$bitpay = new \Bitpay\Bitpay(
    array(
        'bitpay' => array(
            'network'     => 'testnet', // testnet or livenet, default is livenet
            'public_key'  => '/tmp/bitpay.pub', //see tutorial/001.php and 002.php
            'private_key' => '/tmp/bitpay.pri',
            'key_storage' => 'Bitpay\Storage\EncryptedFilesystemStorage',
            'key_storage_password' => 'YourTopSecretPassword'
        )
    )
);

/**
 * Create the client that will be used to send requests to BitPay's API
 */
$client = $bitpay->get('client');

$tokens = $client->getTokens();
print_r($tokens);
