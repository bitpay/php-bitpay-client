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

namespace Bitpay\Client\Adapter;

use Bitpay\Client\RequestInterface;
use Bitpay\Client\ResponseInterface;
use Bitpay\Client\Response;

/**
 * Adapter that sends Request objects using CURL
 *
 * @TODO add way to configure curl with options
 *
 * @codeCoverageIgnore
 * @package Bitpay
 */
class CurlAdapter implements AdapterInterface
{

    /**
     * @inheritdoc
     */
    public function sendRequest(RequestInterface $request)
    {
        $curl = curl_init();
        curl_setopt_array(
            $curl,
            array(
                CURLOPT_URL            => $request->getUri(),
                CURLOPT_PORT           => 443,
                CURLOPT_HTTPHEADER     => $request->getHeaderFields(),
                CURLOPT_TIMEOUT        => 10,
                CURLOPT_SSL_VERIFYPEER => 1,
                CURLOPT_SSL_VERIFYHOST => 2,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FORBID_REUSE   => 1,
                CURLOPT_FRESH_CONNECT  => 1,
                CURLOPT_HEADER         => true,
            )
        );

        if (RequestInterface::METHOD_POST == $request->getMethod()) {
            curl_setopt_array(
                $curl,
                array(
                    CURLOPT_POST           => 1,
                    CURLOPT_POSTFIELDS     => $request->getBody(),
                )
            );
        }

        /** @var ResponseInterface */
        $response = Response::createFromRawResponse(curl_exec($curl));

        curl_close($curl);

        return $response;
    }
}
