<?php

namespace Bitpay\Client;

/**
 *
 * @package Bitpay
 */
interface ResponseInterface
{

    /**
     * @return string
     */
    public function getBody();

    /**
     * Returns the status code of the response
     *
     * @return integer
     */
    public function getStatusCode();

    /**
     * Returns a $key => $value array of http headers
     *
     * @return array
     */
    public function getHeaders();
}
