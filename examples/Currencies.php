<?php
/**
 * Copyright (c) 2014-2015 BitPay
 */

require __DIR__ . '/../vendor/autoload.php';

$bitpay     = new \Bitpay\Bitpay(__DIR__ . '/config.yml');
$client     = $bitpay->get('client');
$currencies = $client->getCurrencies();

/** @var \Bitpay\Currency $currencies[0] **/
var_dump($currencies[0]);
