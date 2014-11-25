<?php

require __DIR__ . '/../vendor/autoload.php';

$client  = new \Bitpay\Client\Client();
$network = new \Bitpay\Network\Testnet();
$adapter = new \Bitpay\Client\Adapter\CurlAdapter();
$client->setNetwork($network);
$client->setAdapter($adapter);
$currencies = $client->getCurrencies();

/** @var \Bitpay\Currency $currencies[0] **/
foreach ($currencies as $currency) {
    echo $currency->getCode().PHP_EOL;
}
