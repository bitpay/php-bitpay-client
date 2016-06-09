<?php
/**
 * Copyright (c) 2014-2015 BitPay
 */

require __DIR__ . '/../vendor/autoload.php';

$client = new \Bitpay\Client\Client();
$client->setAdapter(new \Bitpay\Client\Adapter\CurlAdapter());
$client->setNetwork(new \Bitpay\Network\Testnet());
$request = new \Bitpay\Client\Request();
$request->setHost('test.bitpay.com');
$request->setMethod(\Bitpay\Client\Request::METHOD_GET);
$request->setPath('rates/USD');

$response = $client->sendRequest($request);
$data = json_decode($response->getBody(), true);
var_dump($data);



