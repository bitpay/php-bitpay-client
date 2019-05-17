<?php
/**
 * Copyright (c) 2014-2016 BitPay
 */

require __DIR__ . '/../vendor/autoload.php';

$private = new \Bitpay\PrivateKey();
//if you've got a hex-encoded private key string, you can use it to create a private key
$private->setHex('5555550968bc659873d723374213fa5bf7a30c24f0f0713aa798eb7daa7230fc');
$public = new \Bitpay\PublicKey();
$public->generate($private);
$sin = $public->getSin();

printf("Public Key:  %s\n", $public);
printf("Private Key: %s\n", $private);
printf("Sin Key:     %s\n\n", $sin);

$keypair = array($private->getHex(), $public->getHex());

printf("PEM keypair:  %s\n", $private->pemEncode($keypair));


// -or- if you've got a PEM-encoded text file containing your key pair, we can use this
/*
$keys = file_get_contents(getenv('HOME') . '/.php-bitpay-client/key.pem');
if (isset($keys) && strlen($keys) > 0) {
    $keys = chop($keys);

    $private = new \Bitpay\PrivateKey();
    $private->setHex($private->pemDecode($keys)['private_key']);
    printf("\n\n");
    printf("Public Key:  %s\n", $private->getPublicKey());
    printf("Private Key: %s\n", $private);
    printf("Sin Key:     %s\n\n", $private->getPublicKey()->getSin());
}
*/
