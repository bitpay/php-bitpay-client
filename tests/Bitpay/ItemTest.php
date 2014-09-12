<?php

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
