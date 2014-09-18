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

class AccessTokenTest extends \PHPUnit_Framework_TestCase
{

    public function testId()
    {
        $token = new AccessToken();
        $token->setId('test');

        $this->assertSame('test', $token->getId());
    }

    public function testEmail()
    {
        $token = new AccessToken();
        $token->setEmail('test@test.com');

        $this->assertSame('test@test.com', $token->getEmail());
    }

    public function testLabel()
    {
        $token = new AccessToken();
        $token->setLabel('label');

        $this->assertSame('label', $token->getLabel());
    }

    public function testNonce()
    {
        $token = new AccessToken();
        $this->assertFalse($token->isNonceDisabled());
    }

    public function testNonceDisable()
    {
        $token = new AccessToken();
        $this->assertFalse($token->isNonceDisabled());
        $token->nonceDisable();
        $this->assertTrue($token->isNonceDisabled());
    }

    public function testNonceEnable()
    {
        $token = new AccessToken();
        $this->assertFalse($token->isNonceDisabled());
        $token->nonceEnable();
        $this->assertFalse($token->isNonceDisabled());
    }
}
