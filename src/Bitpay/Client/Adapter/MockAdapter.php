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
use Bitpay\Client\Response;

/**
 * Used to test requests and return a specific response back
 *
 * @codeCoverageIgnore
 * @package Bitpay
 */
class MockAdapter implements AdapterInterface
{

    /**
     * @inheritdoc
     */
    public function sendRequest(RequestInterface $request)
    {
        $path = $request->getPath();
        switch ($path) {
            case ('api/invoice'):
            case ('invoices'):
                $response = $this->createMockInvoice($request);
                break;
            case ('currencies'):
                $response = $this->createMockCurrencies($request);
                break;
            default:
                $response = $this->createMockError($request);
                break;
        }

        return $response;
    }

    private function createMockCurrencies(RequestInterface $request)
    {
        $response = new Response();

        return $response;
    }

    /**
     * Creates a fake response when a testing creating an invoice
     *
     * @param RequestInterface
     *
     * @return ResponseInterface
     */
    private function createMockInvoice(RequestInterface $request)
    {
        $body = json_decode($request->getBody(), true);
        if (empty($body['price'])) {
            return $this->createMockError($request);
        }
        $faker    = \Faker\Factory::create();
        $response = new Response();
        $id       = $faker->randomNumber;
        $response->setBody(
            json_encode(
                array(
                    'id'              => $id,
                    'url'             => sprintf('https://test.bitpay.com/invoices?id=%s', $id),
                    'status'          => 'new',
                    'btcPrice'        => $faker->randomDigit,
                    'price'           => $faker->randomDigit,
                    'invoiceTime'     => $faker->unixTime,
                    'expirationTime'  => $faker->unixTime,
                    'currentTime'     => $faker->unixTime,
                    'btcPaid'         => $faker->randomDigit,
                    'rate'            => $faker->randomFloat(8, 1, 100),
                    'exceptionStatus' => false,
                )
            )
        );

        return $response;
    }

    /**
     * Creates an error response
     *
     * @param RequestInterface $request
     *
     * @return ResponseInterface
     */
    private function createMockError(RequestInterface $request)
    {
        $response = new Response();
        $response->setBody(
            json_encode(
                array(
                    'error' => array(),
                )
            )
        );

        return $response;
    }
}
