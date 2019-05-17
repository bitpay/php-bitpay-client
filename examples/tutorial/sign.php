<?php

require __DIR__.'/../../vendor/autoload.php';

$storageEngine = new \Bitpay\Storage\EncryptedFilesystemStorage('TopSecretPassword');
$private    = $storageEngine->load('/tmp/private_key.key');
$public     = $storageEngine->load('/tmp/public_key.key');
$public->setPrivateKey($private);
$public->generate();

$sin    = new \Bitpay\SinKey('/tmp/sin.key');
$sin->setPublicKey($public);
$sin->generate();


#printf("Public Key:  %s\n", $public);
#printf("Private Key: %s\n", $private);

#$message = 'https://test.bitpay.com/subscriptions{"schedule":"weekly","token":"some token","billData":{"currency":"USD","price":"2.00","quantity":1}}';

$message = 'https://test.bitpay.com/bills{
	{
        "amount":"1.00", 
        "currency":"USD",
        "token":"TfCYYmnL9ZArncyeKCFdNX5V8ArudfiUFTp",
        "description":"netsuite test",
        "email":"joshlewis@gmail.com"
    }';


$signedMessage = $private->sign($message);
error_log('$sin ' . $sin);
error_log('$public ' . $public);


error_log('message to be signed:: ' . $message . "\n");
error_log('signed message:: ' . $signedMessage);
