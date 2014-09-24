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
