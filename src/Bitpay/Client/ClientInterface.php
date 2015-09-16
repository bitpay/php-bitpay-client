<?php
/**
 * @license Copyright 2011-2015 BitPay Inc., MIT License
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay\Client;

use Bitpay\InvoiceInterface;
use Bitpay\PayoutInterface;

/**
 * Interface for class that sends request(s) to BitPay.
 *
 * @package Bitpay
 */
interface ClientInterface
{
    const TESTNET = '0x6F';
    const LIVENET = '0x00';

    /**
     * These can be changed/updated so when the request is sent to BitPay it
     * gives insight into what is making the calls.
     *
     * @see RFC2616 section 14.43 for User-Agent Format
     */
    const NAME    = 'BitPay PHP Client';
    const VERSION = '2.2.6';

    public function getCurrencies();

    /**
     * @param InvoiceInterface $invoiceId
     * @return \Bitpay\Invoice
     * @throws \Exception
     */
    public function createInvoice(InvoiceInterface $invoice);

    /**
     * @param $invoiceId
     * @return InvoiceInterface
     * @throws \Exception
     */
    public function getInvoice($invoiceId);

    /**
     * Create a Payout Request on Bitpay.
     *
     * @param PayoutInterface $payout
     * @return PayoutInterface|mixed
     * @throws \Exception
     */
    public function createPayout(PayoutInterface $payout);

    /**
     * @param null $status
     * @return array
     * @throws \Exception
     */
    public function getPayouts($status = null);

    /**
     * @param $payoutId
     * @return \Bitpay\Payout
     * @throws \Exception
     */
    public function getPayout($payoutId);

    /**
     * @param PayoutInterface
     * @return PayoutInterface|mixed
     * @throws \Exception
     */
    public function deletePayout(PayoutInterface $payout);

    /**
     * Get an array of tokens indexed by facade.
     *
     * @return array
     * @throws \Exception
     */
    public function getTokens();
}
