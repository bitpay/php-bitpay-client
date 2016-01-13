<?php
/**
 * @license Copyright 2011-2015 BitPay Inc., MIT License
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay;

/**
 * Interface PaymentUrlInterface
 * @package Bitpay
 */
interface PaymentUrlInterface
{

    /**
     * This is a list of returned payment url types
     *
     */
    const BIP_21  = 'BIP21';
    const BIP_72  = 'BIP72';
    const BIP_72B = 'BIP72b';
    const BIP_73  = 'BIP73';

    /**
     * Return the account parameter given by Bitpay.
     *
     * @return string
     */
    public function getUrl($urlType);
}
