<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay\Client;

class RequestTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->request = new Request();
    }

    public function testGetHeaderFields()
    {
        $this->assertCount(1, $this->request->getHeaderFields());

        $headers = $this->request->getHeaderFields();
        $this->assertSame('Content-Type: application/json', $headers[0]);
    }

    public function testGetMethod()
    {
        // default method is post
        $this->assertSame(Request::METHOD_POST, $this->request->getMethod());
    }

    /**
     * @depends testGetMethod
     */
    public function testSetMethod()
    {
        $this->request->setMethod(Request::METHOD_GET);
        $this->assertSame(Request::METHOD_GET, $this->request->getMethod());
    }

    /**
     * @depends testSetMethod
     */
    public function testIsMethodGet()
    {
        $this->request->setMethod(Request::METHOD_GET);
        $this->assertTrue($this->request->isMethod(Request::METHOD_GET));
        $this->assertFalse($this->request->isMethod(Request::METHOD_POST));
        $this->assertFalse($this->request->isMethod(Request::METHOD_PUT));
    }

    /**
     * @depends testSetMethod
     */
    public function testIsMethodPost()
    {
        $this->request->setMethod(Request::METHOD_POST);
        $this->assertFalse($this->request->isMethod(Request::METHOD_GET));
        $this->assertTrue($this->request->isMethod(Request::METHOD_POST));
        $this->assertFalse($this->request->isMethod(Request::METHOD_PUT));
    }

    /**
     * @depends testSetMethod
     */
    public function testIsMethodPut()
    {
        $this->request->setMethod(Request::METHOD_PUT);
        $this->assertFalse($this->request->isMethod(Request::METHOD_GET));
        $this->assertFalse($this->request->isMethod(Request::METHOD_POST));
        $this->assertTrue($this->request->isMethod(Request::METHOD_PUT));
    }

    /**
     * @depends testSetMethod
     */
    public function testIsMethodUnknown()
    {
        $this->assertFalse($this->request->isMethod('unknown method'));
    }

    public function testGetPort()
    {
        $this->assertSame(443, $this->request->getPort());
    }

    public function testSetPort()
    {
        $this->request->setPort(444);
        $this->assertSame(444, $this->request->getPort());
    }


    public function testGetSchema()
    {
        $this->assertSame('https', $this->request->getSchema());
    }

    public function testGetHost()
    {
        $this->assertNull($this->request->getHost());
    }

    /**
     * @depends testGetHost
     */
    public function testSetHost()
    {
        $this->request->setHost('test.bitpay.com');
        $this->assertSame('test.bitpay.com', $this->request->getHost());
    }

    public function testGetPath()
    {
        $this->assertNull($this->request->getPath());
    }

    /**
     * @depends testGetPath
     */
    public function testSetPath()
    {
        $this->request->setPath('api/invoice');
        $this->assertSame('api/invoice', $this->request->getPath());
    }

    /**
     * @depends testSetHost
     * @depends testGetPath
     */
    public function testGetUri()
    {
        $this->request->setHost('test.bitpay.com');
        $this->assertSame('https://test.bitpay.com/', $this->request->getUri());
    }

    /**
     * @depends testSetHost
     * @depends testGetPath
     */
    public function testGetUriWithPort()
    {
        $this->request->setHost('test.bitpay.com');
        $this->assertSame('https://test.bitpay.com:443/', $this->request->getUriWithPort());
    }

    public function testGetHeaders()
    {
        $this->assertInternalType('array', $this->request->getHeaders());
    }

    /**
     * @depends testGetHeaders
     */
    public function testSetHeader()
    {
        $this->request->setHeader('Header-Key', 'Header-Value');
        $this->assertArrayHasKey('Header-Key', $this->request->getHeaders());
        $this->assertContains('Header-Value', $this->request->getHeaders());
    }

    /**
     * @depends testSetHeader
     * @expectedException Exception
     */
    public function testSetHeaderException()
    {
        $this->request->setHeader('ting', array());
    }

    public function testGetBody()
    {
        $this->assertNull($this->request->getBody());
    }

    /**
     * @depends testGetBody
     */
    public function testSetBody()
    {
        $this->request->setBody('{"json":true}');
        $this->assertSame('{"json":true}', $this->request->getBody());
    }

    /**
     * @depends testGetMethod
     * @depends testGetUri
     * @depends testGetHeaders
     * @depends testGetBody
     */
    public function testToStringWithoutBody()
    {
        $this->request->setHost('test.bitpay.com');
        $raw = array(
            'POST https://test.bitpay.com:443/ HTTP/1.1',
            'Content-Type: application/json',
        );
        $raw = implode("\r\n", $raw);
        $this->assertSame($raw, (string) $this->request);
    }

    /**
     * @depends testGetMethod
     * @depends testGetUri
     * @depends testGetHeaders
     * @depends testGetBody
     */
    public function testToStringWithBody()
    {
        $this->request->setHost('test.bitpay.com');
        $this->request->setBody('{"json":true}');
        $raw = array(
            'POST https://test.bitpay.com:443/ HTTP/1.1',
            'Content-Type: application/json',
            'Content-Length: 13',
            '',
            '{"json":true}',
        );
        $raw = implode("\r\n", $raw);
        $this->assertSame($raw, (string) $this->request);
    }
}
