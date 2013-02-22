php-client
==========

PHP library for the Bitpay.com API

To use, fill in relivent information in the bp_options.php file.

use 
    bpCreateInvoice($orderId, $price, $posData, $options = array())
to rise an invoice, where.

    $orderId: Used to display an orderID to the buyer. In the account summary view, this value is used to identify a ledger entry if present.
    $price: by default, $price is expressed in the currency you set in bp_options.php.  The currency can be changed in $options.
    $posData: this field is included in status updates or requests to get an invoice.  It is intended to be used by the merchant to uniquely identify an order associated with an invoice in their system.  Aside from that, Bit-Pay does not use the data in this field.  The data in this field can be anything that is meaningful to the merchant.
    $options keys can include any of: 
    ('itemDesc', 'itemCode', 'notificationEmail', 'notificationURL', 'redirectURL', 'apiKey'
      'currency', 'physical', 'fullNotifications', 'transactionSpeed', 'buyerName', 
    'buyerAddress1', 'buyerAddress2', 'buyerCity', 'buyerState', 'buyerZip', 'buyerEmail', 'buyerPhone')

If a given option is not provided here, the value of that option will default to what is found in bp_options.php
 (see api documentation for information on these options).

and
    bpGetInvoice($invoiceId, $apiKey=false)
to check it's status.
