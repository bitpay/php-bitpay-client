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
        setlocale(LC_NUMERIC, 'en_US');
        // Accepts floats
        $this->item->setPrice(9.99);
        $this->assertSame(9.99, $this->item->getPrice());

        // Accepts integers
        $this->item->setPrice(9);
        $this->assertSame(9.0, $this->item->getPrice());

        // Accepts standard float string
        $this->item->setPrice("9.99");
        $this->assertNotNull($this->item->getPrice());
        $this->assertSame(9.99, $this->item->getPrice());
        $this->assertInternalType('float', $this->item->getPrice());

        $this->item->setPrice("0.999999");
        $this->assertNotNull($this->item->getPrice());
        $this->assertSame(.999999, $this->item->getPrice());
        $this->assertInternalType('float', $this->item->getPrice());

        // if the argument is a string
        // and the string is not "floatlike"
        // throw an error
    }

    /**
     *  @expectedException \Bitpay\Client\ArgumentException
     */
    public function testBadStringPrice()
    {
        // Accepts standard integer string
        $this->item->setPrice("456");
        $this->assertSame(456.0, $this->item->getPrice());

        // Accepts float string without leading integer
        $this->item->setPrice(".99");
        $this->assertSame(0.99, $this->item->getPrice());
        $this->item->setPrice(".0");
        $this->assertSame(0.0, $this->item->getPrice());

        // Accepts float string without decimal numbers
        $this->item->setPrice("9.");
        $this->assertSame(9.0, $this->item->getPrice());

        // Arbitrary precision
        $this->item->setPrice("9.4329082");
        $this->assertSame(9.4329082, $this->item->getPrice());

        // Try with different locale
        setlocale(LC_NUMERIC, 'it_IT');

        // Accepts floats
        $this->item->setPrice(9.99);
        $this->assertSame(9.99, $this->item->getPrice());

        // Accepts integers
        $this->item->setPrice(9);
        $this->assertSame(9.0, $this->item->getPrice());

        // Accepts standard float string
        $this->item->setPrice("9.99");
        $this->assertSame(9.99, $this->item->getPrice());

        // Accepts standard integer string
        $this->item->setPrice("456");
        $this->assertSame(456.0, $this->item->getPrice());

        // Accepts float string without leading integer
        $this->item->setPrice(".99");
        $this->assertSame(0.99, $this->item->getPrice());
        $this->item->setPrice(".0");
        $this->assertSame(0.0, $this->item->getPrice());

        // Accepts float string without decimal numbers
        $this->item->setPrice("9.");
        $this->assertSame(9.0, $this->item->getPrice());

        // Arbitrary precision
        $this->item->setPrice("9.4329082");
        $this->assertSame(9.4329082, $this->item->getPrice());
        setlocale(LC_NUMERIC, 'en_US');
    }

    /**
     * @expectedException        \Bitpay\Client\ArgumentException
     */
    public function testSetPriceExceptionNoNumber()
    {
        $this->item->setPrice(".");
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
