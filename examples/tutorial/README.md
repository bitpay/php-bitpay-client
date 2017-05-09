Tutorial - Introduction
==========================

In this tutorial I will give an example of going through all the steps required
to get up and running with BitPay using the PHP SDK with code examples.

The code that you find here SHOULD run needing only to modify a few values.

001_generateKeys.php : generates the private/public keys to sign the communication with BitPay. The private/public keys are stored in your filesystem for later usage.
002_pair.php : pairs your private/public keys to your BitPay merchant account. The result is an API token that can be used to create invoices permanently.
^^ the above two scripts need to be executed only once.

003_createInvoice.php : creates a BitPay invoice. Please make sure to fill in the API token received from 003_createInvoice.php
^^ this last script can be executed permanently using the API token.


* Testing * 
To begin, please visit https://test.bitpay.com and register for a test account.
If you are looking for a testnet wallet to test with, please visit https://bitpay.com/wallet and
create a new wallet. 

For more information about testing, please see https://bitpay.com/docs/testing


Once you have an account and a testnet wallet, you should begin to go through
this tutorial starting from file `001.php` and continuing on to the next
files in order.


Examples (c) 2014-2017 BitPay
