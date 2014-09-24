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

namespace Bitpay;

class TokenTest extends \PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $token = new Token();
        $this->assertInstanceOf('Bitpay\TokenInterface', $token);
    }

    public function testToString()
    {
        $token = new Token();
        $this->assertSame('', (string) $token);
    }

    public function testGetToken()
    {
        $token = new Token();
        $this->assertNull($token->getToken());
    }

    /**
     * @depends testGetToken
     */
    public function testSetToken()
    {
        $token = new Token();
        $token->setToken('test');
        $this->assertSame('test', $token->getToken());
    }

    public function testGetResource()
    {
        $token = new Token();
        $this->assertNull($token->getResource());
    }

    /**
     * @depends testGetResource
     */
    public function testSetResource()
    {
        $token = new Token();
        $token->setResource('test');
        $this->assertSame('test', $token->getResource());
    }

    public function testGetFacade()
    {
        $token = new Token();
        $this->assertNull($token->getFacade());
    }

    /**
     * @depends testGetFacade
     */
    public function testSetFacade()
    {
        $token = new Token();
        $token->setFacade('pos');
        $this->assertSame('pos', $token->getFacade());
    }

    public function testGetCreatedAt()
    {
        $token = new Token();
        $this->assertNull($token->getCreatedAt());
    }

    /**
     * @depends testGetCreatedAt
     */
    public function testSetCreatedAt()
    {
        $token = new Token();
        $token->setCreatedAt('createdAt');
        $this->assertSame('createdAt', $token->getCreatedAt());
    }

    public function testGetPolicies()
    {
        $token = new Token();
        $this->assertCount(0, $token->getPolicies());
    }

    /**
     * @depends testGetPolicies
     */
    public function testSetPolicies()
    {
        $token = new Token();
        $token->setPolicies('policy');
        $this->assertSame('policy', $token->getPolicies());
    }
}
