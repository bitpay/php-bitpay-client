<?php

namespace Bitpay;

/**
 * This is the currency code set for the price setting.  The pricing currencies
 * currently supported are USD, EUR, BTC, and all of the codes listed on this page:
 * https://bitpay.com/bitcoin­exchange­rates
 *
 * @package Bitpay
 */
interface CurrencyInterface
{

    /**
     * @return string
     */
    public function getCode();

    /**
     * @return string
     */
    public function getSymbol();

    /**
     * @return string
     */
    public function getPrecision();

    /**
     * @return string
     */
    public function getExchangePctFee();

    /**
     * @return string
     */
    public function isPayoutEnabled();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getPluralName();

    /**
     * @return string
     */
    public function getAlts();

    /**
     * @return string
     */
    public function getPayoutFields();
}
