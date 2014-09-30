<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay;

class ItemTest extends \PHPUnit_Framework_TestCase
{
    private $item;

    public function setUp()
    {
        $this->item = new Item();
    }

    public function testGetCode()
    {
        $this->assertNotNull($this->item);
        $this->assertNull($this->item->getCode());
    }

    /**
     * @depends testGetCode
     */
    public function testSetCode()
    {
        $this->item->setCode('Code');
        $this->assertNotNull($this->item->getCode());
        $this->assertSame('Code', $this->item->getCode());
    }

    public function testGetDescription()
    {
        $this->assertNotNull($this->item);
        $this->assertNull($this->item->getDescription());
    }

    /**
     * @depends testGetDescription
     */
    public function testSetDescription()
    {
        $this->item->setDescription('Description of Item');
        $this->assertNotNull($this->item->getDescription());
        $this->assertSame('Description of Item', $this->item->getDescription());
    }

    public function testGetPrice()
    {
        $this->assertNotNull($this->item);
        $this->assertNull($this->item->getPrice());
    }

    /**
     * @depends testGetPrice
     */
    public function testSetPrice()
    {
        $this->item->setPrice(9.99);
        $this->assertNotNull($this->item->getPrice());
        $this->assertSame(9.99, $this->item->getPrice());
        $float = (9.99 === $this->item->getPrice() ? true: false);

        $this->item->setPrice("9.99");
        $this->assertNotNull($this->item->getPrice());
        $this->assertSame("9.99", $this->item->getPrice());
        $string = ("9.99" === $this->item->getPrice() ? true: false);
        if((true === $string) && (true === $float)) {
          echo "setPrice accepts both strings and floats";
        }
    }

    public function testGetQuantity()
    {
        $this->assertNotNull($this->item);
        $this->assertNull($this->item->getQuantity());
    }

    /**
     * @depends testGetQuantity
     */
    public function testSetQuantity()
    {
        $this->item->setQuantity(1);
        $this->assertNotNull($this->item->getQuantity());
        $this->assertSame(1, $this->item->getQuantity());
    }

    public function testIsPhysical()
    {
        $this->assertNotNull($this->item);
        $this->assertFalse($this->item->isPhysical());
    }

    /**
     * @depends testIsPhysical
     */
    public function testSetPhysicalTrue()
    {
        $this->item->setPhysical(true);
        $this->assertNotNull($this->item->isPhysical());
        $this->assertTrue($this->item->isPhysical());
    }
}
