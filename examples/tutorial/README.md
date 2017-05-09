# Tutorial
==========================

This tutorial contains four scripts:

Script 001 & 002 need to be executed once, to properly configure your local installation.

Actual BitPay invoice creation is done in script 003; this script can be run permanently.

The last script (IPNLogger) processes BitPay's Instant Payment Notifications.

## Script 1 & 2: configuring your local installation
The following two scripts need to be executed once. These scripts will generate your private/public keys and pair them to your BitPay merchant account:
1. 001_generateKeys.php : generates the private/public keys to sign the communication with BitPay. The private/public keys are stored in your filesystem for later usage.
2. 002_pair.php : pairs your private/public keys to your BitPay merchant account. The result is an API token that can be used to create invoices permanently.


# Script 3: creating BitPay invoices
3. 003_createInvoice.php : creates a BitPay invoice. Please make sure to fill in the API token received from 003_createInvoice.php
This last script (003_createInvoice.php) can be executed permanently using the API token.

# Script 4: process IPNs
IPNLogger.php processes IPNs (Instant Payment Notifications). This script should be put on your server and be reachable from the internet. Your should put the URL of IPNLogger.php in your invoice creation script, e.g.:
`$invoice
    // You will receive IPN's at this URL, should be HTTPS for security purposes!
    ->setNotificationUrl('http://yourserver.com/IPNlogger.php');
`
For more information about IPNs, please see https://bitpay.com/docs/invoice-callbacks


## Testing
To begin, please visit https://test.bitpay.com and register for a test account.
If you are looking for a testnet wallet to test with, please visit https://bitpay.com/wallet and
create a new wallet. 

For more information about testing, please see https://bitpay.com/docs/testing



Examples (c) 2014-2017 BitPay
