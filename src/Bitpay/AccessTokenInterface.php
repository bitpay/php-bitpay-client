<?php

namespace Bitpay;

/**
 * Creates an access token for the given client
 *
 * @package Bitpay
 */
interface AccessTokenInterface
{

    /**
     * @return string
     */
    public function getId();

    /**
     * @return string
     */
    public function getEmail();

    /**
     * @return string
     */
    public function getLabel();

    /**
     * @return boolean
     */
    public function isNonceDisabled();
}
