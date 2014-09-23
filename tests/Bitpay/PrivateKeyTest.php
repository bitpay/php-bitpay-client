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

class PrivateKeyTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $this->assertInstanceOf('Bitpay\PrivateKey', PrivateKey::create());
    }

    public function testGenerate()
    {
        $priKey = new PrivateKey();
        $this->assertNotNull($priKey);

        $this->assertNull($priKey->getHex());
        $this->assertNull($priKey->getDec());

        $priKey->generate();

        $this->assertEquals(64, strlen($priKey->getHex()));
        $this->assertGreaterThanOrEqual(76, strlen($priKey->getDec()));
    }

    public function testGetHex()
    {
        $priKey = new PrivateKey();
        $this->assertNotNull($priKey);

        $this->assertNull($priKey->getHex());

        $this->assertEquals(0, strlen($priKey->getHex()));

        $priKey->generate();

        $this->assertEquals(64, strlen($priKey->getHex()));
    }

    /**
     * @depends testGetHex
     * @depends testGenerate
     */
    public function testToString()
    {
        $priKey = new PrivateKey();
        $this->assertNotNull($priKey);

        // Make sure this is a string
        $this->assertInternalType('string', $priKey->__toString());

        $this->assertSame('', (string) $priKey);

        $priKey->generate();

        // make sure it's still a string after generating hex
        $this->assertInternalType('string', $priKey->__toString());
    }

    public function testGetDec()
    {
        $priKey = new PrivateKey();
        $this->assertNotNull($priKey);

        $this->assertNull($priKey->getDec());

        $this->assertEquals(0, strlen($priKey->getDec()));

        $priKey->generate();

        $this->assertGreaterThanOrEqual(76, strlen($priKey->getDec()));
    }

    public function testIsValid()
    {
        $priKey = new PrivateKey();
        $this->assertNotNull($priKey);

        $this->assertFalse($priKey->isValid());

        $priKey->generate();

        $this->assertTrue($priKey->isValid());
    }

    public function testSign()
    {
        $priKey = new PrivateKey();
        $this->assertNotNull($priKey);

        $auth = new BitAuth();

        $priKey->generate();

        //$signature = $priKey->sign('BitPay');

        $signature = $auth->sign('BitPay', $priKey);
        $this->assertNotNull($signature);
    }

    public function testHasValidHex()
    {
        $priKey = new PrivateKey();
        $this->assertNotNull($priKey);

        $this->assertFalse($priKey->hasValidHex());

        $priKey->generate();

        $this->assertTrue($priKey->hasValidHex());
    }

    public function testHasValidDec()
    {
        $priKey = new PrivateKey();
        $this->assertNotNull($priKey);

        $this->assertFalse($priKey->hasValidDec());

        $priKey->generate();

        $this->assertTrue($priKey->hasValidDec());
    }

    /**
     * @see https://github.com/bitpay/bitcore/blob/master/test/test.PrivateKey.js
     */
    public function testCreateFromHex()
    {
        $hex      = 'b9f4892c9e8282028fea1d2667c4dc5213564d41fc5783896a0d843fc15089f3';
        $expected = 'cTpB4YiyKiBcPxnefsDpbnDxFDffjqJob8wGCEDXxgQ7zQoMXJdH';

        $key = PrivateKey::createFromHex($hex);
        //$this->assertSame($hex, (string) $key);
    }
}
