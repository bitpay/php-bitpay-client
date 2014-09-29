<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay\Client;

class ResponseTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->response = new Response();
    }

    public function testGetStatusCode()
    {
        $this->assertNull($this->response->getStatusCode());
    }

    public function testGetBody()
    {
        $this->assertNull($this->response->getBody());
    }

    /**
     * @depends testGetBody
     */
    public function testSetBody()
    {
        $this->response->setBody('{"json":true}');
        $this->assertSame('{"json":true}', $this->response->getBody());
    }

    public function testGetHeaders()
    {
        $this->assertInternalType('array', $this->response->getHeaders());
    }

    /**
     * @depends testGetHeaders
     */
    public function testSetHeader()
    {
        $this->response->setHeader('Header-Key', 'Header-Value');
        $this->assertArrayHasKey('Header-Key', $this->response->getHeaders());
        $this->assertContains('Header-Value', $this->response->getHeaders());
    }

    public function testCreateFromRawResponse()
    {
        $response = Response::createFromRawResponse($this->getTestResponse200());
        $this->assertSame(200, $response->getStatusCode());
    }

    /**
     * @depends testGetStatusCode
     * @depends testGetHeaders
     * @depends testGetBody
     */
    public function testToString()
    {
        $response = new Response($this->getTestResponse200());
        $this->assertSame($this->getTestResponse200(), (string) $response);
    }

    private function getTestResponse200()
    {
        return <<<EOF
HTTP/1.1 200 OK
Content-Type: text/xml; charset=utf-8

{
  "response": "example"
}
EOF;
    }
}
