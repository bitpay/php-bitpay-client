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

namespace Bitpay\Util;

/**
 * @package Bitcore
 *
 * @requires function openssl_random_pseudo_bytes
 */
class SecureRandomTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->enableOpenSSL();
    }

    public function testHasOpenSSL()
    {
        $this->assertTrue(SecureRandom::hasOpenSSL());
        $this->disableOpenSSL();
        $this->assertFalse(SecureRandom::hasOpenSSL());
    }

    public function testGenerateRandom()
    {
        $randomNumber = SecureRandom::generateRandom();
        $this->assertEquals(32, strlen($randomNumber));
    }

    /**
     * @expectedException Exception
     */
    public function testWithoutOpenssl()
    {
        $this->disableOpenSSL();

        SecureRandom::generateRandom();
    }

    /**
     * @expectedException Exception
     */
    public function testWithoutStrongCryptographicNumber()
    {
        SecureRandom::generateRandom(0);
    }

    private function disableOpenSSL()
    {
        $ref = new \ReflectionProperty('Bitpay\Util\SecureRandom', 'hasOpenSSL');
        $ref->setAccessible(true);
        $ref->setValue(false);
        $ref->setAccessible(false);
    }

    private function enableOpenSSL()
    {
        $ref = new \ReflectionProperty('Bitpay\Util\SecureRandom', 'hasOpenSSL');
        $ref->setAccessible(true);
        $ref->setValue(null);
        $ref->setAccessible(false);
    }
}
