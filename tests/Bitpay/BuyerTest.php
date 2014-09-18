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

class BuyerTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->user = new Buyer();
    }

    public function testGetPhone()
    {
        $this->assertNull($this->user->getPhone());
    }

    /**
     * @depends testGetPhone
     */
    public function testSetPhone()
    {
        $this->user->setPhone('555-555-5555');
        $this->assertSame('555-555-5555', $this->user->getPhone());
    }

    public function testGetEmail()
    {
        $this->assertNull($this->user->getEmail());
    }

    /**
     * @depends testGetEmail
     */
    public function testSetEmail()
    {
        $this->user->setEmail('josh@bitpay.com');
        $this->assertSame('josh@bitpay.com', $this->user->getEmail());
    }

    public function testGetFirstName()
    {
        $this->assertNull($this->user->getFirstName());
    }

    /**
     * @depends testGetFirstName
     */
    public function testSetFirstName()
    {
        $this->user->setFirstName('Joshua');
        $this->assertSame('Joshua', $this->user->getFirstName());
    }

    public function testGetLastName()
    {
        $this->assertNull($this->user->getLastName());
    }

    /**
     * @depends testGetLastName
     */
    public function testSetLastName()
    {
        $this->user->setLastName('Estes');
        $this->assertSame('Estes', $this->user->getLastName());
    }

    public function testGetAddress()
    {
        $this->assertNull($this->user->getAddress());
    }

    /**
     * @depends testGetAddress
     */
    public function testSetAddress()
    {
        $addr = array(
            '123 Main St',
            'Suite 100',
        );
        $this->user->setAddress($addr);
        $this->assertSame($addr, $this->user->getAddress());
    }

    public function testGetCity()
    {
        $this->assertNull($this->user->getCity());
    }

    /**
     * @depends testGetCity
     */
    public function testSetCity()
    {
        $this->user->setCity('Atlanta');
        $this->assertSame('Atlanta', $this->user->getCity());
    }

    public function testGetState()
    {
        $this->assertNull($this->user->getState());
    }

    /**
     * @depends testGetState
     */
    public function testSetState()
    {
        $this->user->setState('GA');
        $this->assertSame('GA', $this->user->getState());
    }

    public function testGetZip()
    {
        $this->assertNull($this->user->getZip());
    }

    /**
     * @depends testGetZip
     */
    public function testSetZip()
    {
        $this->user->setZip('37379');
        $this->assertSame('37379', $this->user->getZip());
    }
}
