<?php

require __DIR__ . '/../vendor/autoload.php';



$a = "123";
$b = "234";

$key = new \Bitpay\PrivateKey();
$key->generate();
echo $key;
