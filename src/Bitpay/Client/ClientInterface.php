<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay\Client;

use Bitpay\CurrencyInterface;
use Bitpay\InvoiceInterface;
use Bitpay\PayoutInterface;
use Bitpay\RateInterface;
use Bitpay\SupportRequestInterface;

/**
 * Sends request(s) to bitpay server
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
    const NAME = 'BitPay PHP-Client';
    const VERSION = '0.0.0';


    //public function createApplication(ApplicationInterface $application);

    //public function createBill(BillInterface $bill);
    //public function getBills($status = null);
    //public function getBill($billId);
    //public function updateBill(BillInterface $bill);

    //public function createAccessToken(AccessTokenInterface $accessToken);
    //public function getAccessTokens();
    //public function getAccessToken($keyId);

    public function getCurrencies();

    /**
     * @param InvoiceInterface $invoice
     *
     * @return \Bitpay\Invoice
     * @throws \Exception
     */
    public function createInvoice(InvoiceInterface $invoice);
    //public function getInvoices();

    /**
     * @param $invoiceId
     *
     * @return InvoiceInterface
     * @throws \Exception
     */
    public function getInvoice($invoiceId);

    /**
     * @param $invoiceId
     * @param $bitcoinAddress
     * @param $amount
     * @param $currency
     *
     * @return SupportRequestInterface
     * @throws \Exception
     */
    public function createRefund($invoiceId, $bitcoinAddress, $amount, $currency);


    /**
     * Returns the status of a refund.
     *
     * @param $invoiceId
     * @param $refundRequestId
     *
     * @return SupportRequestInterface
     * @throws \Exception
     */
    public function getRefund($invoiceId, $refundRequestId);


    /**
     * Cancels a pending refund request
     *
     * @param $invoiceId
     * @param $refundRequestId
     *
     * @return mixed
     * @throws \Exception
     */
    public function cancelRefund($invoiceId, $refundRequestId);


    /**
     * Returns the status of all refunds on an invoice.
     *
     * @param $invoiceId
     *
     * @return SupportRequestInterface[]
     * @throws \Exception
     */
    public function getRefunds($invoiceId);

    //public function getLedgers();
    //public function getLedger(CurrencyInterface $currency);

    //public function getOrgs();
    //public function getOrg($orgId);
    //public function updateOrg(OrgInterface $org);

    /**
     * Create a Payout Request on Bitpay
     *
     * @param PayoutInterface $payout
     *
     * @return PayoutInterface|mixed
     * @throws \Exception
     */
    public function createPayout(PayoutInterface $payout);

    /**
     * @param null $status
     *
     * @return array
     * @throws \Exception
     */
    public function getPayouts($status = null);

    /**
     * @param $payoutId
     *
     * @return \Bitpay\Payout
     * @throws \Exception
     */
    public function getPayout($payoutId);

    /**
     * @param PayoutInterface $payout
     *
     * @return PayoutInterface|mixed
     * @throws \Exception
     */
    public function deletePayout(PayoutInterface $payout);

    //public function updatePayout(PayoutInterface $payout);


    /**
     * Retrieves the list of exchange rates.
     * @return RateInterface[]
     */
    public function getRates();


    /**
     * Retrieves the exchange rate for the given currency.
     *
     * @param CurrencyInterface $currency
     *
     * @return RateInterface
     */
    public function getRate(CurrencyInterface $currency);

    /**
     * Get an array of tokens indexed by facade
     * @return array
     * @throws \Exception
     */
    public function getTokens();

    //public function getUser();
    //public function updateUser(UserInterface $user);
}
