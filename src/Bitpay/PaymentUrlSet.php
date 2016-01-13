<?php
/**
 * @license Copyright 2011-2015 BitPay Inc., MIT License
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay;

/**
 * @package Bitpay
 */
class PaymentUrlSet implements PaymentUrlInterface
{

    /**
     * @var array
     */
    protected $paymentUrls;


    public function __construct(array $paymentUrls = array())
    {
        $this->paymentUrls = $paymentUrls;
    }

    /**
     * Return the account parameter given by Bitpay.
     *
     * @return string
     */
    public function getUrl($urlType)
    {
        if (array_key_exists($urlType, $this->paymentUrls)) {
            return $this->paymentUrls[$urlType];
        }

        return null;
    }

    /**
     * Return the account parameter given by Bitpay.
     *
     * @return PaymentUrlInterface
     */
    public function setUrls(array $paymentUrls)
    {
        $this->paymentUrls = $paymentUrls;

        return $this;
    }
}
