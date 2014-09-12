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

/**
 * @package Bitcore
 */
class SinKeyTest extends \PHPUnit_Framework_TestCase
{

    public function testToString()
    {
        $pubKey = PublicKey::create();
        $pubKey->setPrivateKey(PrivateKey::create()->generate());
        $pubKey->generate();
        $sinKey = new SinKey();
        $sinKey->setPublicKey($pubKey);
        $this->assertSame('', (string) $sinKey);
        $sinKey->generate();
        $this->assertEquals(35, strlen((string) $sinKey));
    }

    /**
     * @expectedException Exception
     */
    public function testGenerateWithException()
    {
        $sinKey = new SinKey();
        $sinKey->setPublicKey(PublicKey::create());
        $sinKey->generate();
    }

    public function testGenerateWithoutException()
    {
        $pubKey = PublicKey::create();
        $pubKey->setPrivateKey(PrivateKey::create()->generate());
        $pubKey->generate();
        $sinKey = new SinKey();
        $sinKey->setPublicKey($pubKey);
        $sinKey->generate();
        $this->assertEquals(35, strlen((string) $sinKey));
    }
}
