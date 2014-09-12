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

use Bitpay\Bill;

class BillTest extends \PHPUnit_Framework_TestCase
{
    private $bill;

    public function setUp()
    {
        $this->bill = new Bill();
    }

    public function testGetItems()
    {
        $this->assertInternalType('array', $this->bill->getItems());
        $this->assertEmpty($this->bill->getItems());
    }

    /**
     * @depends testGetItems
     */
    public function testAddItem()
    {
        $this->bill->addItem($this->getMockItem());
        $this->assertCount(1, $this->bill->getItems());
    }

    public function testGetCurrency()
    {
        $this->assertInstanceOf('Bitpay\CurrencyInterface', $this->bill->getCurrency());
    }

    /**
     * @depends testGetCurrency
     */
    public function testSetCurrency()
    {
        $this->bill->setCurrency($this->getMockCurrency());
        $this->assertInstanceOf('Bitpay\CurrencyInterface', $this->bill->getCurrency());
    }

    public function testGetName()
    {
        $this->assertNull($this->bill->getName());
    }

    /**
     * @depends testGetName
     */
    public function testSetName()
    {
        $this->bill->setName('Joshua Estes');
        $this->assertSame('Joshua Estes', $this->bill->getName());
    }

    public function testGetAddress()
    {
        $this->assertEmpty($this->bill->getAddress());
    }

    /**
     * @depends testGetAddress
     */
    public function testSetAddress()
    {
        $addr = array(
            '123 Main St',
            'Suite 200',
        );
        $this->bill->setAddress($addr);
        $this->assertSame($addr, $this->bill->getAddress());
    }

    public function testGetCity()
    {
        $this->assertNull($this->bill->getCity());
    }

    /**
     * @depends testGetCity
     */
    public function testSetCity()
    {
        $this->bill->setCity('Atlanta');
        $this->assertSame('Atlanta', $this->bill->getCity());
    }

    public function testGetState()
    {
        $this->assertNull($this->bill->getState());
    }

    /**
     * @depends testGetState
     */
    public function testSetState()
    {
        $this->bill->setState('GA');
        $this->assertSame('GA', $this->bill->getState());
    }

    public function testGetZip()
    {
        $this->assertNull($this->bill->getZip());
    }

    /**
     * @depends testGetZip
     */
    public function testSetZip()
    {
        $this->bill->setZip('37379');
        $this->assertSame('37379', $this->bill->getZip());
    }

    public function testGetCountry()
    {
        $this->assertNull($this->bill->getCountry());
    }

    /**
     * @depends testGetCountry
     */
    public function testSetCountry()
    {
        $this->bill->setCountry('US');
        $this->assertSame('US', $this->bill->getCountry());
    }

    public function testGetEmail()
    {
        $this->assertNull($this->bill->getEmail());
    }

    /**
     * @depends testGetEmail
     */
    public function testSetEmail()
    {
        $this->bill->setEmail('josh@bitpay.com');
        $this->assertSame('josh@bitpay.com', $this->bill->getEmail());
    }

    public function testGetPhone()
    {
        $this->assertNull($this->bill->getPhone());
    }

    /**
     * @depends testGetPhone
     */
    public function testSetPhone()
    {
        $this->bill->setPhone('555-555-5555');
        $this->assertSame('555-555-5555', $this->bill->getPhone());
    }

    public function testGetStatus()
    {
        $this->assertNull($this->bill->getStatus());
    }

    /**
     * @depends testGetStatus
     */
    public function testSetStatus()
    {
        $this->bill->setStatus('unknown');
        $this->assertSame('unknown', $this->bill->getStatus());
    }

    public function testGetShowRate()
    {
        $this->assertNull($this->bill->getShowRate());
    }

    /**
     * @depends testGetShowRate
     */
    public function testSetShowRate()
    {
        $this->bill->setShowRate('unknown');
        $this->assertSame('unknown', $this->bill->getShowRate());
    }

    public function testGetArchived()
    {
        $this->assertFalse($this->bill->getArchived());
    }

    /**
     * @depends testGetArchived
     */
    public function testSetArchived()
    {
        $this->bill->setArchived(true);
        $this->assertTrue($this->bill->getArchived());
    }

    private function getMockItem()
    {
        return $this->getMock('Bitpay\ItemInterface');
    }

    private function getMockCurrency()
    {
        return $this->getMock('Bitpay\CurrencyInterface');
    }
}
