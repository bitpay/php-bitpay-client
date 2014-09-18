<?php
/**
 * The MIT License (MIT)
 *
 * Copyright (c) 2014 BitPay, Inc.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace Bitpay\Client;

/**
 *
 * @package Bitpay
 */
interface RequestInterface
{
    const METHOD_POST   = 'POST';
    const METHOD_GET    = 'GET';
    const METHOD_PUT    = 'PUT';

    /**
     * Returns the method for this request
     *
     * @return string
     */
    public function getMethod();

    /**
     * Should always return https
     *
     * @return string
     */
    public function getSchema();

    /**
     * Returns the host to send the request to. The host would be something
     * such as `test.bitpay.com` or `bitpay.com`
     *
     * @return string
     */
    public function getHost();

    /**
     * Returns port to send request on
     *
     * @return integer
     */
    public function getPort();

    /**
     * example of path is `api/invoice` as this is appended to $host
     *
     * @return string
     */
    public function getPath();

    /**
     * Returns $schema://$host:$port/$path
     *
     * @return string
     */
    public function getUri();

    /**
     * Checks the request to see if the method matches a known value
     *
     * @param string $method
     *
     * @return boolean
     */
    public function isMethod($method);

    /**
     * Returns the request body
     *
     * @return string
     */
    public function getBody();

    /**
     * Returns a $key => $value array of http headers
     *
     * @return array
     */
    public function getHeaders();
}
