<?php
/*
This IPN url is set when creating an invoice, modify as needed to integrate with your system
This also uses the EXTENDED notifications, the preferred way to receive IPN updates on every status of your invoice
 */

/*
autoload the classes

buttons
client
config
invoice
item
 */

function BPC_autoloader($class)
{
    if (strpos($class, 'BPC_') !== false):
        if (!class_exists('BitPayLib/' . $class, false)):
            #doesnt exist so include it
            include 'BitPayLib/' . $class . '.php';
        endif;
    endif;
}
spl_autoload_register('BPC_autoloader');

$data = $request->get_body();

$data = json_decode($data);
$event = $data->event;
$data = $data->data;

$orderid = $data->orderId;
$order_status = $data->status;
$invoiceID = $data->id;

/* We recommend double checking the invoice data by calling the Invoice API endpoint, instead of relying 100% on incoming notifications */

/*Create your configuration object, passing the $environment of 'test' or 'production'*/
$config = new BPC_Configuration($bitpay_checkout_token, $environment);

/*Start building your parameters object to send to the API, filling in the details with your own data*/
$params = new stdClass();

$params->invoiceID = $invoiceID;

$item = new BPC_Item($config, $params);

$invoice = new BPC_Invoice($item); //this creates the invoice with all of the config params
$orderStatus = json_decode($invoice->BPC_checkInvoiceStatus($invoiceID));

switch ($event->name) {
    case 'invoice_confirmed':
    if ($orderStatus->data->status == 'confirmed'):
        /* The invoice has gone through the confirmation and is complete */
        /* Do your custom integration here */   
    endif;
    break;

    case 'invoice_paidInFull':
    if ($orderStatus->data->status == 'paid'):
        /* The default state when a user pays for an invoice but has not confirmed */
        /* Do your custom integration here */   
    endif;
    break;

    case 'invoice_failedToConfirm':
    if ($orderStatus->data->status == 'invalid'):
        /* There's an error, most likely network congestion, so update any order access */
        /* Do your custom integration here */   
    endif;
    break;

    case 'invoice_expired':
    if ($orderStatus->data->status == 'expired'):
        /* An invoice was created but never paid, default is 15 minutes.  Many users delete the order and access in their system at this state */
        /* Do your custom integration here */   
    endif;
    break;

    case 'invoice_refundComplete':
    if ($orderStatus->data->status == 'refunded'):
        /* Invoice is refunded */
        /* Do your custom integration here */   
    endif;
    break;
}
