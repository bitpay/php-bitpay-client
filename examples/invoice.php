<?php
/*
Create an invoice using your API Token.
API @ https://bitpay.com/api
Create your token @ https://test.bitpay.com/dashboard/merchant/api-tokens or https:/.bitpay.com/dashboard/merchant/api-tokens
*/

/*
Autoload the classes
 */
function BPC_autoloader($class)
{
    #change the pathing if needed
    if (strpos($class, 'BPC_') !== false):
        if (!class_exists('../BitPayLib/' . $class, false)):
            #doesnt exist so include it
            include '../BitPayLib/' . $class . '.php';
        endif;
    endif;
}
spl_autoload_register('BPC_autoloader');


/* replace these variables */
$bitpay_checkout_token = '<your api token>';
$environment = 'test';
$order_id = 1;

/*Create your configuration object, passing the $environment of 'test' or 'production'*/
$config = new BPC_Configuration($bitpay_checkout_token, $environment);

/*Start building your parameters object to send to the API, filling in the details with your own data*/
$params = new stdClass();
/* your plugin version */
$params->extension_version = 'MyApp_1.0.0';

$params->price = '1.00';
$params->currency = 'USD';

/*if you would like to add the users email to the order so they do not have to add it themselves, use the following*/
$buyerInfo = new stdClass();
$buyerInfo->name = 'First name Last Name';
$buyerInfo->email = 'email@address.com';
$params->buyer = $buyerInfo;

/*$order_id is required, an order has to be made before being sent to the API to link both systems */
$params->orderId = trim($order_id);

/* Needs to be set to send users to after they pay their invoice
You can pass parameters to handle any custom interaction when they are redirected
 */
$params->redirectURL = 'http://mywebsite.com/redirect.php?variable=123';

/*
The IPN is optional, used to update your system as needed when payment changes.
This won't appear in the invoice object
*/
$params->notificationURL = 'http://mywebsite.com/ipn.php';

$params->extendedNotifications = true;
$params->transactionSpeed = 'medium';
$params->acceptanceWindow = 1200000;

/* Create an item from the configuration and parameters */
$item = new BPC_Item($config, $params);

/* Create the invoice parameters from the item */
$invoice = new BPC_Invoice($item);

/* Create the Invoice from the API*/
$invoice->BPC_createInvoice();

/* This is optional but will have the data from the invoice of if you need it for other purposes */
$invoiceData = json_decode($invoice->BPC_getInvoiceData());

#leave this to show the output on the command line
echo 'Invoice URL: '.$invoiceData->data->url.PHP_EOL;
echo(print_r($invoiceData,true));



/* EXAMPLE OF AN INVOICE FROM ABOVE */

/*
{
facade: "public/invoice",
data: {
url: "https://test.bitpay.com/invoice?id=5HiJtWmLCL8xcUtdhmekRC",
status: "new",
price: 1,
currency: "USD",
invoiceTime: 1560260860804,
expirationTime: 1560261760804,
currentTime: 1560260867734,
id: "5HiJtWmLCL8xcUtdhmekRC",
lowFeeDetected: false,
amountPaid: 0,
exceptionStatus: false,
redirectURL: "http://myserver.com/redirect.php",
refundAddressRequestPending: false,
buyerProvidedInfo: {<this will have the name and email if provided>},
paymentSubtotals: {
BTC: 12800,
BCH: 260600
},
paymentTotals: {
BTC: 12900,
BCH: 260600
},
paymentDisplayTotals: {
BTC: "0.000129",
BCH: "0.002606"
},
paymentDisplaySubTotals: {
BTC: "0.000128",
BCH: "0.002606"
},
exchangeRates: {
BTC: {
USD: 7809.999999999999,
BCH: 20.349140177175606
},
BCH: {
USD: 383.7,
BTC: 0.04913756340692229
}
},
supportedTransactionCurrencies: {
BTC: {
enabled: true
},
BCH: {
enabled: true
}
},
minerFees: {
BTC: {
satoshisPerByte: 1.008,
totalFee: 100
},
BCH: {
satoshisPerByte: 0,
totalFee: 0
}
},
paymentCodes: {
BTC: {
BIP72b: "bitcoin:?r=https://test.bitpay.com/i/5HiJtWmLCL8xcUtdhmekRC",
BIP73: "https://test.bitpay.com/i/5HiJtWmLCL8xcUtdhmekRC"
},
BCH: {
BIP72b: "bitcoincash:?r=https://test.bitpay.com/i/5HiJtWmLCL8xcUtdhmekRC",
BIP73: "https://test.bitpay.com/i/5HiJtWmLCL8xcUtdhmekRC"
}
},
token: "<random guid goes here></random>"
}
}
*/
