<?php
/**
 * Copyright (c) 2014-2017 BitPay
 *
 * 004 - IPN logger
 *
 * Requirements:
 *   - Account on https://test.bitpay.com
 *   - Baisic PHP Knowledge
 *   - Private and Public keys from 001.php
 *   - Token value obtained from 002.php
 *   - Invoice created & paid
 */
require __DIR__.'/../../vendor/autoload.php';


$myfile = fopen("/tmp/BitPayIPN.log", "a");

$raw_post_data = file_get_contents('php://input');

$date = date('m/d/Y h:i:s a', time());

if (false === $raw_post_data) {
    fwrite($myfile, $date . " : Error. Could not read from the php://input stream or invalid Bitpay IPN received.\n");
    fclose($myfile);
    throw new \Exception('Could not read from the php://input stream or invalid Bitpay IPN received.');
}

$ipn = json_decode($raw_post_data);

if (true === empty($ipn)) {
    fwrite($myfile, $date . " : Error. Could not decode the JSON payload from BitPay.\n");
    fclose($myfile);
    throw new \Exception('Could not decode the JSON payload from BitPay.');
}

if (true === empty($ipn -> id)) {
    fwrite($myfile, $date . " : Error. Invalid Bitpay payment notification message received - did not receive invoice ID.\n");
    fclose($myfile);
    throw new \Exception('Invalid Bitpay payment notification message received - did not receive invoice ID.');
}

// Now fetch the invoice from BitPay
// This is needed, since the IPN does not contain any authentication

$client        = new \Bitpay\Client\Client();
$network       = new \Bitpay\Network\Testnet();
//$network = new \Bitpay\Network\Livenet();
$adapter       = new \Bitpay\Client\Adapter\CurlAdapter();
$client->setNetwork($network);
$client->setAdapter($adapter);

$token = new \Bitpay\Token();
$token->setToken('UpdateThisValue'); // UPDATE THIS VALUE
$client->setToken($token);

/**
 * This is where we will fetch the invoice object
 */
$invoice = $client->getInvoice($ipn->id);
$invoiceId = $invoice->getId();
$invoiceStatus = $invoice->getStatus();
$invoiceExceptionStatus = $invoice->getExceptionStatus();
$invoicePrice = $invoice->getPrice();

fwrite($myfile, $date . " : IPN received for BitPay invoice ".$invoiceId." . Status = " .$invoiceStatus." / exceptionStatus = " . $invoiceExceptionStatus." Price = ". $invoicePrice. "\n");
fwrite($myfile, "Raw IPN: ". $raw_post_data."\n");

//Respond with HTTP 200, so BitPay knows the IPN has been received correctly
//If BitPay receives <> HTTP 200, then BitPay will try to send the IPN again with increasing intervals for two more hours.
header("HTTP/1.1 200 OK");
?>