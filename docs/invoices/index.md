##  Invoices
Creating an invoice allows you to accept payment in bitcoins. You can
also query the BitPay's system to find out more information about the
invoice that you created.

Working with an Invoice Object
==============================

Every invoice can have lots of data that can be used and sent to BitPay
as reference. Feel free to take a look at `Bitpay\InvoiceInterface` for
code comments on what each method returns and a more in depth
explanation.

First we need to create a new Invoice object.

``` {.sourceCode .php}
$invoice = new \Bitpay\Invoice();
```

> **note**
>
> You can also set an Order ID that you can use to reference BitPay's
> invoice with the invoice in your order system.
>
> `$invoice->setOrderId('You Order ID here')`

To make an invoice valid, it needs a price and a currency. You can see a
list of currencies supported by viewing the [Bitcoin Exchange
Rates](https://bitpay.com/bitcoin-exchange-rates) page on our website.

For this example, we will use `USD` as our currency of choice.

``` {.sourceCode .php}
$invoice->setCurrency(new \Bitpay\Currency('USD'));
```

Now the invoice knows what currency to use. Next it needs a price.

``` {.sourceCode .php}
$item = new \Bitpay\Item();
$item->setPrice('19.95');
$invoice->setItem($item);
```

The only thing left is to now send the invoice off to BitPay for the
invoice to be created and for you to send it to your customer.

Creating an Invoice
===================

Create an instance of the Bitpay class.

``` {.sourceCode .php}
$bitpay = new \Bitpay\Bitpay(
    array(
        'bitpay' => array(
            'network'     => 'testnet', // testnet or livenet, default is livenet
            'public_key'  => getenv('HOME').'/.bitpay/api.pub',
            'private_key' => getenv('HOME').'/.bitpay/api.key',
        )
    )
);
```

> **warning**
>
> If you are running a command line script as a different user, you
> could get a different \$HOME directory. Please be aware. Also the keys
> are chmod'ed when written to disk so the private key can only be read
> by the owner.

Next you will need to get the client.

``` {.sourceCode .php}
// @var \Bitpay\Client\Client
$client = $bitpay->get('client');
```

Inject your `TokenObject` into the client.

``` {.sourceCode .php}
$token = new \Bitpay\Token();
$token->setToken('Insert Token Here');
$client->setToken($token);
```

Now all you need to do is send the `$invoice` object to Bitpay.

``` {.sourceCode .php}
$client->createInvoice($invoice);
```

The code will update the `$invoice` object and you will be able to
forward your customer to BitPay's fullscreen invoice.

``` {.sourceCode .php}
header('Location: ' . $invoice->getUrl());
```

Instant Payment Notifications (IPN)
===================================

You can enabled IPNs for an invoice by setting the notificationUrl.
Example:

``` {.sourceCode .php}
$invoice->setNotificationUrl('https://example.com/bitpay/ipn');
```

By adding the Notification URL, it will receive an IPN when the invoice
is updated. For more information on IPNs, please see the documentation
on BitPay's website.
