========
Invoices
========

Creating an invoice allows you to accept payment in bitcoins. You can also query
the BitPay's system to find out more information about the invoice that you
created.

Working with an Invoice Object
==============================

Every invoice can have lots of data that can be used and sent to BitPay as
reference. Feel free to take a look at ``Bitpay\InvoiceInterface`` for code
comments on what each method returns and a more in depth explanation.

First we need to create a new Invoice object.

.. code-block:: php

    $invoice = new \Bitpay\Invoice();

.. note::

    You can also set an Order ID that you can use to reference BitPay's invoice
    with the invoice in your order system.

    ``$invoice->setOrderId('You Order ID here')``

To make an invoice valid, it needs a price and a currency. You can see a list
of currencies supported by viewing the `Bitcoin Exchange Rates <https://bitpay.com/bitcoin-exchange-rates>`_
page on our website.

For this example, we will use ``USD`` as our currency of choice.

.. code-block:: php

    $currency = new \Bitpay\Currency();
    $currency->setCode('USD');
    $invoice->setCurrency($currency);

Now the invoice knows what currency to use. Next it needs a price.

.. code-block:: php

    $item = new \Bitpay\Item();
    $item->setPrice('19.95');
    $invoice->setItem($item);

The only thing left is to now send the invoice off to BitPay for the invoice
to be created and for you to send it to your customer.


Creating an Invoice
===================

Create an instance of the Bitpay class.

.. code-block:: php

    $bitpay = new \Bitpay\Bitpay(
        array(
            'bitpay' => array(
                'network'     => 'testnet', // testnet or livenet, default is livenet
                'public_key'  => getenv('HOME').'/.bitpay/api.pub',
                'private_key' => getenv('HOME').'/.bitpay/api.key',
            )
        )
    );

Next you will need to get the client.

.. code-block:: php

    // @var \Bitpay\Client\Client
    $client = $bitpay->get('client');

Now all you need to do is send the ``$invoice`` object to Bitpay.

.. code-block:: php

    $client->createInvoice($invoice);

The code will update the ``$invoice`` object and you will be able to forward
your customer to BitPay's fullscreen invoice.

.. code-block:: php

    header('Location: ' . $invoice->getUrl());
