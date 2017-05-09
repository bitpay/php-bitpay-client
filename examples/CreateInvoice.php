<?php
/**
 * Copyright (c) 2014-2015 BitPay
 *
 * WARNING - This example will NOT work until you have generated your public
 * and private keys. Please see the example documentation on generating your
 * keys and also see the documentation on how to save those keys.
 *
 * Also please be aware that you CANNOT create an invoice until you have paired
 * the keys and received a token back. The token is usesd with the request.
 */

require __DIR__ . '/../vendor/autoload.php';

/**
 * Create an Item object that will be used later
 */
$item = new \Bitpay\Item();
$item
    ->setCode('skuNumber')
    ->setDescription('General Description of Item')
    ->setPrice('1.99');

/**
 * Create Buyer object that will be used later.
 */
$buyer = new \Bitpay\Buyer();
$buyer
    ->setFirstName('Some')
    ->setLastName('Customer')
    ->setPhone('555-5555-5555')
    ->setEmail('test@test.com')
    ->setAddress(
        array(
            '123 Main St',
            'Suite 1',
        )
    )
    ->setCity('Atlanta')
    ->setState('GA')
    ->setZip('30120')
    ->setCountry('US');

/**
 * Create the invoice
 */
$invoice = new \Bitpay\Invoice();
// Add the item to the invoice
$invoice->setItem($item);
// Add the buyers info to invoice
$invoice->setBuyer($buyer);
// Configure the rest of the invoice
$invoice
    ->setOrderId('OrderIdFromYourSystem')
    // You will receive IPN's at this URL, should be HTTPS for security purposes!
    ->setNotificationUrl('https://store.example.com/bitpay/callback');

/**
 * BitPay offers services for many different currencies. You will need to
 * configure the currency in which you are selling products with.
 */
$currency = new \Bitpay\Currency();
$currency->setCode('USD');

// Set the invoice currency
$invoice->setCurrency($currency);

/**
 * Create a new client. You can see the example of how to configure this using
 * a yml file as well.
 */
$bitpay = new \Bitpay\Bitpay(__DIR__ . '/config.yml');

/**
 * Create the client that will be used to send requests to BitPay's API
 */
$client = $bitpay->get('client');

/**
 * You will need to set the token that was returned when you paired your
 * keys.
 */
$token = new \Bitpay\Token();
$token->setToken('your token here');

$client->setToken($token);

// Send invoice
$client->createInvoice($invoice);

var_dump(
    (string) $client->getRequest(),
    (string) $client->getResponse(),
    $invoice
);
