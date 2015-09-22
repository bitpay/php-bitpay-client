<?php
/**
 * @license Copyright 2011-2015 BitPay Inc., MIT License
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay\Client\Adapter;

use Bitpay\Client\RequestInterface;
use Bitpay\Client\ResponseInterface;
use Bitpay\Client\Response;

/**
 * Adapter class that sends Request objects using cURL.
 *
 * @package Bitpay
 */
class CurlAdapter implements AdapterInterface
{
    /**
     * @var array
     */
    protected $curlOptions;

    /**
     * @param array $curlOptions
     */
    public function __construct(array $curlOptions = array())
    {
        $this->curlOptions = $curlOptions;
    }

    /**
     * Returns an array of cURL settings to use
     *
     * @return array
     */
    public function getCurlOptions()
    {
        return $this->curlOptions;
    }

    /**
     * @inheritdoc
     */
    public function sendRequest(\Bitpay\Client\Request $request)
    {
        $curl = curl_init();

        if ($curl === false) {
            throw new \Exception('[ERROR] In CurlAdapter::sendRequest(): Could not initialize cURL.');
        }

        $default_curl_options = $this->getCurlDefaultOptions($request);

        foreach ($this->getCurlOptions() as $curl_option_key => $curl_option_value) {
            if (is_null($curl_option_value) === false) {
                $default_curl_options[$curl_option_key] = $curl_option_value;
            }
        }

        curl_setopt_array($curl, $default_curl_options);

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

        if ($raw === false) {
            $errorMessage = curl_error($curl);
            curl_close($curl);

            throw new \Exception('[ERROR] In CurlAdapter::sendRequest(): curl_exec failed with the error "' . $errorMessage . '".');
        }

        /** @var Response */
        $response = Response::createFromRawResponse($raw);

        curl_close($curl);

        return $response;
    }

    /**
     * Returns an array of default cURL settings to use.
     *
     * @param \Bitpay\Client\Request $request
     * @return array
     */
    private function getCurlDefaultOptions(\Bitpay\Client\Request $request)
    {
        return array(
            CURLOPT_URL            => $request->getUri(),
            CURLOPT_PORT           => $request->getPort(),
            CURLOPT_CUSTOMREQUEST  => $request->getMethod(),
            CURLOPT_HTTPHEADER     => $request->getHeaderFields(),
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_SSL_VERIFYPEER => 1,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_CAINFO         => __DIR__ . '/ca-bundle.crt',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FORBID_REUSE   => 1,
            CURLOPT_FRESH_CONNECT  => 1,
            CURLOPT_HEADER         => true,
        );
    }
}
