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

use Bitpay\Item;

class ItemTest extends \PHPUnit_Framework_TestCase
{
    private $item;

    public function setUp()
    {
        $this->item = new Item();
    }

    public function testGetCode()
    {
        $this->assertNull($this->item->getCode());
    }

    /**
     * @depends testGetCode
     */
    public function testSetCode()
    {
        $this->item->setCode('Code');
        $this->assertSame('Code', $this->item->getCode());
    }

    public function testGetDescription()
    {
        $this->assertNull($this->item->getDescription());
    }

    /**
     * @depends testGetDescription
     */
    public function testSetDescription()
    {
        $this->item->setDescription('Description of Item');
        $this->assertSame('Description of Item', $this->item->getDescription());
    }

    public function testGetPrice()
    {
        $this->assertNull($this->item->getPrice());
    }

    /**
     * @depends testGetPrice
     */
    public function testSetPrice()
    {
        $this->item->setPrice(9.99);
        $this->assertSame(9.99, $this->item->getPrice());
    }

    public function testGetQuantity()
    {
        $this->assertNull($this->item->getQuantity());
    }

    /**
     * @depends testGetQuantity
     */
    public function testSetQuantity()
    {
        $this->item->setQuantity(1);
        $this->assertSame(1, $this->item->getQuantity());
    }

    public function testIsPhysical()
    {
        $this->assertFalse($this->item->isPhysical());
    }

    /**
     * @depends testIsPhysical
     */
    public function testSetPhysicalTrue()
    {
        $this->item->setPhysical(true);
        $this->assertTrue($this->item->isPhysical());
    }
}
