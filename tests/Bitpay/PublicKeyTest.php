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

class PublicKeyTest extends \PHPUnit_Framework_TestCase
{

    public function testCreate()
    {
        $this->assertInstanceOf('Bitpay\PublicKey', PublicKey::create());
    }

    public function testGenerate()
    {
        $priKey = new PrivateKey();
        $this->assertNotNull($priKey);

        $priKey->generate();

        $pubKey = new PublicKey();
        $this->assertNotNull($pubKey);

        $pubKey->setPrivateKey($priKey);

        $this->assertNull($pubKey->getHex());
        $this->assertNull($pubKey->getDec());

        $pubKey->generate();

        $this->assertEquals(130, strlen($pubKey->getHex()));
        $this->assertGreaterThanOrEqual(155, strlen($pubKey->getDec()));
    }

    /**
     * @depends testGenerate
     */
    public function testGetHex()
    {
        $pubKey = new PublicKey();
        $this->assertNotNull($pubKey);

        $pubKey->setPrivateKey(PrivateKey::create()->generate());

        $this->assertNull($pubKey->getHex());

        $pubKey->generate(PrivateKey::create()->generate());

        $this->assertEquals(130, strlen($pubKey->getHex()));
    }

    /**
     * @depends testGenerate
     */
    public function testGetDec()
    {
        $pubKey = new PublicKey();
        $this->assertNotNull($pubKey);

        $pubKey->setPrivateKey(PrivateKey::create()->generate());

        $this->assertNull($pubKey->getDec());

        $pubKey->generate(PrivateKey::create()->generate());
        //$this->assertGreaterThanOrEqual(155, strlen($pubKey->getDec()));
    }

    /**
     * @see https://github.com/bitpay/bitcore/blob/master/test/test.Key.js
     * @depends testGenerate
     */
    public function testToString()
    {
        $pubKey = new PublicKey();
        $this->assertNotNull($pubKey);

        $pubKey->setPrivateKey(PrivateKey::create()->generate());

        $this->assertSame('', (string) $pubKey);

        $pubKey->generate(PrivateKey::create()->generate());

        if ('02'.$pubKey->getX() == $pubKey) {
            $compressed = '02'.$pubKey->getX();
        } else {
            $compressed = '03'.$pubKey->getX();
        }

        $this->assertSame($compressed, (string) $pubKey);
        // compress is 33 length
        //$this->assertEquals(33, strlen((string) $pubKey));
        // uncompresses is 66 length
        //$this->assertEquals(66, strlen((string) $pubKey));
    }

    /**
     * @depends testGenerate
     */
    public function testGetX()
    {
        $pubKey = new PublicKey();
        $this->assertNotNull($pubKey);

        $pubKey->setPrivateKey(PrivateKey::create()->generate());

        $this->assertNull($pubKey->getX());

        $pubKey->generate(PrivateKey::create()->generate());

        $this->assertEquals(64, strlen($pubKey->getX()));
    }

    /**
     * @depends testGenerate
     */
    public function testGetY()
    {
        $pubKey = new PublicKey();
        $this->assertNotNull($pubKey);

        $pubKey->setPrivateKey(PrivateKey::create()->generate());

        $this->assertNull($pubKey->getY());

        $pubKey->generate(PrivateKey::create()->generate());

        $this->assertEquals(64, strlen($pubKey->getY()));
    }

    private function getMockPrivateKey()
    {
        return $this->getMockBuilder('Bitpay\PrivateKey')
            ->getMock();
    }
}
