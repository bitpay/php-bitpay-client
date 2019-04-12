<?php
/**
 * Copyright (c) 2014-2015 BitPay
 *
 * 003 - Creating Invoices
 *
 * Requirements:
 *   - Account on https://test.bitpay.com
 *   - Basic PHP Knowledge
 *   - API Key
 */

function dump($p){
    echo '<pre>';
    print_r($p);

}

require './classes/Config.php';
require './classes/Client.php';
require './classes/Item.php';
require './classes/Invoice.php';

# Create and set this key from https://test.bitpay.com/dashboard/merchant/api-tokens
# UNCHECK Require Authentication if you are only creating invoices
#leave variable 2 as null for testnet, 'production' for livenet

$config = new Configuration('JjQ6uQdQyZoRifE4Arqgwi'); #<your key will go here>

  
#sample values to create an item, should be passed as an object'
$params = new stdClass();
$params->price = '2.00';
$params->currency = 'USD'; #set as needed
$params->buyers_email = 'jlewis@bitpay.com'; #set as needed
#use other fields as needed from API Doc

#preconfigure the BTC or BTCH?  Set the parameter
$params->buyerSelectedTransactionCurrency = 'BTC'; #set as needed



$item = new Item($config,$params);
$invoice = new Invoice($item); #this creates the invoice with all of the config params
$invoice->createInvoice();

#getInvoiceURL is the actual URL returned with the transaction
#$print_r($invoice->getInvoiceURL());
print_r($invoice->getInvoiceData());












