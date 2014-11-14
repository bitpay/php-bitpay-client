<?php

require __DIR__ . '/../vendor/autoload.php';

$key = '0x6363b0691a739ca88fa43f8fd5036300ebf3179b3aa7a65d94f119c12a10f2a3';
$key = new \Bitpay\PrivateKey();
$key->generate();
$pub = $key->getPublicKey();

// $point = new \Bitpay\Point(
//             '0x'.substr(\Bitpay\Util\Secp256k1::G, 2, 64),
//             '0x'.substr(\Bitpay\Util\Secp256k1::G, 66, 64)
//         );

// $gmp = \Bitpay\Util\Gmp::gmpPointAdd($point, $point);
// $util = \Bitpay\Util\Util::pointAdd($point, $point);

var_dump($key);
var_dump($pub);
var_dump($pub->getSin());
