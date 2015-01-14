##  Invoice States
At any given time, invoices are in one of many different states.

> **note**
>
> If you want to get an IPN each time an invoice is updated, when you
> create the invoice, set the `fullNotifications` option to true.

new
===

The initial state of an invoice. When the invoice is created it will be
given this state.

paid
====

A paid state means that a payment has been received. This can be the
full payment, an under payment, or an over payment. This state SHOULD
not be confused with meaning the invoice has been paid in full is that
it is ok to continue to process the order in your system.

confirmed
=========

This state means that based upon your transaction speed that the payment
received is confirmed.

complete
========

This means that BitPay has credited the Merchants account with the
payment received.
