<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */
namespace Bitpay;

/**
 * Interface OrganizationInterface
 * @package Bitpay
 */
interface OrganizationInterface
{

    /**
     * @return string
     */
    public function getName();

    /**
     * @return array [$address1, $address2]
     */
    public function getAddress();

    /**
     * @return string
     */
    public function getCity();

    /**
     * @return string
     */
    public function getState();

    /**
     * @return string
     */
    public function getZip();

    /**
     * @return string
     */
    public function getCountry();

    /**
     * @return boolean
     */
    public function getIsNonProfit();

    /**
     * @return string
     */
    public function getUSTaxId();

    /**
     * @return string
     */
    public function getIndustry();

    /**
     * @return string
     */
    public function getWebsite();

    /**
     * @return string
     */
    public function getCartPOS();

    /**
     * @return string
     */
    public function getAffiliateOID();
}
