<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
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
                CURLOPT_CAINFO         => __DIR__ . '/ca-bundle.crt',
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

        $raw = curl_exec($curl);

        if (false === $raw) {
            curl_close($curl);
            throw new \Exception(curl_error($curl));
        }

        /** @var ResponseInterface */
        $response = Response::createFromRawResponse($raw);

        curl_close($curl);

        return $response;
    }
}
