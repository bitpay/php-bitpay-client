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

require __DIR__ . '/../vendor/autoload.php';

$time = gmdate("Y-m-d\TH:i:s\.", 1414691179)."000Z";

$token = new \Bitpay\Token();
$token
	->setFacade('payroll')
	->setToken('<api token>'); //this is a special api that requires a explicit payroll relationship with BitPay

$instruction1 = new \Bitpay\PayoutInstruction();
$instruction1
	->setAmount(100)
	->setAddress('2NA5EVH9HHHhM5RxSEWf54gP4v397EmFTxi')
	->setLabel('Paying Chris');

$payout = new \Bitpay\Payout();
$payout
	->setEffectiveDate($time)
	->setAmount(100)
	->setCurrency(new \Bitpay\Currency('USD'))
	->setPricingMethod('bitcoinbestbuy')
	->setReference('a reference, can be json')
	->setNotificationEmail('<email address of person to get payout></email>')
	->setNotificationUrl('https://example.com/ipn.php')
	->setToken($token)
	->addInstruction($instruction1);


//this is your private key in some form (see GetKeys.php)
$storageEngine = new \Bitpay\Storage\EncryptedFilesystemStorage('TopSecretPassword');
$private    = $storageEngine->load('/tmp/private_key.key');

$public = new \Bitpay\PublicKey();
$public->generate($private);

$network = new \Bitpay\Network\Testnet();
$adapter = new \Bitpay\Client\Adapter\CurlAdapter();


$bitpay = new \Bitpay\Bitpay();

$client = new \Bitpay\Client\Client();
$client->setPrivateKey($private);
$client->setPublicKey($public);
$client->setNetwork($network);
$client->setAdapter($adapter);

$client->createPayout($payout);

print_r($payout);
