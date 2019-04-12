<?php

require __DIR__ . '/../vendor/autoload.php';
#require __DIR__.'/../../vendor/autoload.php';



#$storageEngine = new \Bitpay\Storage\FilesystemStorage();
$storageEngine = new \Bitpay\Storage\EncryptedFilesystemStorage('YourTopSecretPassword');


$private = $storageEngine->load('/tmp/bitpay.pri');
$public  = new \Bitpay\PublicKey('/tmp/bitpay.pub');
error_log('$private '.$private);
$public->setPrivateKey($private);
$public->generate();

printf("Public Key:  %s\n", $public);
printf("Private Key: %s\n", $private);

$message = 'https://test.bitpay.com/subscriptions{"schedule":"weekly","token":"some token","billData":{"currency":"USD","price":"2.00","quantity":1}}';

$signedMessage = $private->sign($message);
error_log('message to be signed:: ' . $message . "\n");
error_log('signed message:: ' . $signedMessage);
