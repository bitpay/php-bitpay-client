<?php

namespace Bitpay\Client;

use Bitpay\InvoiceInterface;

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
    const NAME    = 'BitPay PHP-Client';
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

    public function createInvoice(InvoiceInterface $invoice);
    //public function getInvoices();
    //public function getInvoice($invoiceId);

    //public function getLedgers();
    //public function getLedger(CurrencyInterface $currency);

    //public function getOrgs();
    //public function getOrg($orgId);
    //public function updateOrg(OrgInterface $org);

    //public function createPayout(PayoutInterface $payout);
    //public function getPayouts($status = null);
    //public function getPayout($payoutId);
    //public function updatePayout(PayoutInterface $payout);

    //public function getRates();
    //public function getRate(CurrencyInterface $currency);

    //public function getTokens();

    //public function getUser();
    //public function updateUser(UserInterface $user);
}
