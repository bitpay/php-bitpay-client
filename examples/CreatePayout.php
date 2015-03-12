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

$time = gmdate("Y-m-d\TH:i:s\.", 1414691179)."000Z";

$token = new \Bitpay\Token();
$token
	->setFacade('payroll')
	->setToken('your token here');
	
$instruction1 = new \Bitpay\PayoutInstruction();
$instruction1
	->setAmount(990)
	->setAddress('n3Sx4askJeykUYk24sS5rQoCEm8BpwVrNg')
	->setLabel('Paying Tom');
	
$instruction2 = new \Bitpay\PayoutInstruction();
$instruction2
	->setAmount(1490)
	->setAddress('mxRN6AQJaDi5R6KmvMaEmZGe3n5ScV9u33')
	->setLabel('Paying Harry');

$payout = new \Bitpay\Payout();
$payout
	->setEffectiveDate($time)
	->setCurrency(new \Bitpay\Currency('USD'))
	->setPricingMethod('bitcoinbestbuy')
	->setReference('a reference, can be json')
	->setNotificationEmail('your@email.com')
	->setNotificationUrl('https://yoursite.com/callback')
	->setToken($token)
	->addInstruction($instruction1)
	->addInstruction($instruction2);	


/**
 * Create a new client. You can see the example of how to configure this using
 * a yml file as well.
 */
$bitpay = new \Bitpay\Bitpay(
    array(
        'bitpay' => array(
            'network'     => 'testnet', // testnet or livenet, default is livenet
            'public_key'  => getenv('HOME').'/.bitpayphp/api.pub',
            'private_key' => getenv('HOME').'/.bitpayphp/api.key',
            'key_storage' => 'Bitpay\Storage\FilesystemStorage',
        )
    )
);

/**
 * Create the client that will be used to send requests to BitPay's API
 */
$client = $bitpay->get('client');

$client->createPayout($payout);

print_r($payout);
