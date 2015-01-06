<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay\Client\Adapter;

use Bitpay\Client\Request;

class CurlAdapterTest extends \PHPUnit_Framework_TestCase
{
    protected $request;

    public function setUp()
    {
        $this->request = new Request();
    }

    public function testConstruct()
    {
        $adapter = new CurlAdapter();
        $this->assertNotNull($adapter->getCurlOptions());
    }

    public function testGetCurlOptions()
    {
        $adapter = new CurlAdapter();
        $this->assertEquals(array(), $adapter->getCurlOptions());
    }

    public function testSendRequestWithException()
    {
        $this->setExpectedException('Bitpay\Client\ConnectionException');
        
        $curl_options = array(
            CURLOPT_URL            => "www.example.com",
            CURLOPT_SSL_VERIFYPEER => 1,
            CURLOPT_SSL_VERIFYHOST => 2,
        );

        $adapter = new CurlAdapter($curl_options);
        $response = $adapter->sendRequest($this->request);
    }

    public function testSendRequestWithoutException()
    {
        $curl_options = array(
            CURLOPT_URL            => "www.bitpay.com",
            CURLOPT_SSL_VERIFYPEER => 1,
            CURLOPT_SSL_VERIFYHOST => 2,
        );

        $adapter = new CurlAdapter($curl_options);
        $response = $adapter->sendRequest($this->request);
        $this->assertNotNull($response);
    }

}