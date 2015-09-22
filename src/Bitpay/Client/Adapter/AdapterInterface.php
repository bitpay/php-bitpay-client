<?php
/**
 * @license Copyright 2011-2015 BitPay Inc., MIT License
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay\Client\Adapter;

use Bitpay\Client\RequestInterface;
use Bitpay\Client\ResponseInterface;

/**
 * A client can be configured to use a specific adapter to make requests, by
 * default the CurlAdapter is what is used.
 *
 * @package Bitpay
 */
interface AdapterInterface
{
    /**
     * Send a request to BitPay.
     *
     * @param \Bitpay\Client\Request $request
     * @return \Bitpay\Client\Response
     */
    public function sendRequest(\Bitpay\Client\Request $request);
}
