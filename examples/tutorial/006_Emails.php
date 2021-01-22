<?php
/**
 * Copyright (c) 2014-2015 BitPay
 */

/**
 * WARNING - This example will NOT work until you have generated your public
 * keys and also see the documentation on how to save those keys.
 *
 * Also please be aware that you CANNOT create an invoice until you have paired
 * the keys and received a token back. The token is usesd with the request.
 */

$key_dir = '/tmp';
require __DIR__.'/../../vendor/autoload.php';

$time = gmdate("Y-m-d\TH:i:s\.", 1414691179)."000Z";

$token = "<your api token>";

//this is your private key in some form (see GetKeys.php)
$storageEngine = new \Bitpay\Storage\EncryptedFilesystemStorage('TopSecretPassword');
$private = $storageEngine->load($key_dir . '/bitpay.pri');
$public = $storageEngine->load($key_dir . '/bitpay.pub');

$network = new \Bitpay\Network\Testnet();
$adapter = new \Bitpay\Client\Adapter\CurlAdapter();


$bitpay = new \Bitpay\Bitpay();

$client = new \Bitpay\Client\Client();
$client->setPrivateKey($private);
$client->setPublicKey($public);
$client->setNetwork($network);
$client->setAdapter($adapter);

#create a batch of email addresses to invite to create a BitPay account

$emails[] = 
array(
    "email"=>"email-address1@email.com",
    "notificationURL" =>"https://test.com/ipn",
    "label"=>"email test 1"
    );

$emails[] = 
array(
    "email"=>"email-address2@email.com",
    "notificationURL" =>"https://test.com/ipn",
    "label"=>"email test 2"
    );
$response = $client->createRecipient($token,$emails);
print_r($response);
