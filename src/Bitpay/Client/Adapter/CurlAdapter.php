<?php

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
