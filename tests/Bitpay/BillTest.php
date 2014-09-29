<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay;

class BillTest extends \PHPUnit_Framework_TestCase
{
    private $bill;

    public function setUp()
    {
        $this->bill = new Bill();
    }

    public function testGetItems()
    {
        $this->assertNotNull($this->bill);
        $this->assertInternalType('array', $this->bill->getItems());
        $this->assertEmpty($this->bill->getItems());
    }

    /**
     * @depends testGetItems
     */
    public function testAddItem()
    {
        $this->assertNotNull($this->bill);
        $this->bill->addItem($this->getMockItem());
        $this->assertCount(1, $this->bill->getItems());
    }

    public function testGetCurrency()
    {
        $this->assertNotNull($this->bill);
        $this->assertInstanceOf('Bitpay\CurrencyInterface', $this->bill->getCurrency());
    }

    /**
     * @depends testGetCurrency
     */
    public function testSetCurrency()
    {
        $this->assertNotNull($this->bill);
        $this->bill->setCurrency($this->getMockCurrency());
        $this->assertInstanceOf('Bitpay\CurrencyInterface', $this->bill->getCurrency());
    }

    public function testGetName()
    {
        $this->assertNotNull($this->bill);
        $this->assertNull($this->bill->getName());
    }

    /**
     * @depends testGetName
     */
    public function testSetName()
    {
        $this->assertNotNull($this->bill);
        $this->bill->setName('BitPay Inc');
        $this->assertSame('BitPay Inc', $this->bill->getName());
    }

    public function testGetAddress()
    {
        $this->assertNotNull($this->bill);
        $this->assertEmpty($this->bill->getAddress());
    }

    /**
     * @depends testGetAddress
     */
    public function testSetAddress()
    {
        $this->assertNotNull($this->bill);

        $addr = array(
                      '3405 Piedmont Rd NE',
                      'Suite 200',
                     );

        $this->bill->setAddress($addr);
        $this->assertSame($addr, $this->bill->getAddress());
    }

    public function testGetCity()
    {
        $this->assertNotNull($this->bill);
        $this->assertNull($this->bill->getCity());
    }

    /**
     * @depends testGetCity
     */
    public function testSetCity()
    {
        $this->assertNotNull($this->bill);
        $this->bill->setCity('Atlanta');
        $this->assertSame('Atlanta', $this->bill->getCity());
    }

    public function testGetState()
    {
        $this->assertNotNull($this->bill);
        $this->assertNull($this->bill->getState());
    }

    /**
     * @depends testGetState
     */
    public function testSetState()
    {
        $this->assertNotNull($this->bill);
        $this->bill->setState('GA');
        $this->assertSame('GA', $this->bill->getState());
    }

    public function testGetZip()
    {
        $this->assertNotNull($this->bill);
        $this->assertNull($this->bill->getZip());
    }

    /**
     * @depends testGetZip
     */
    public function testSetZip()
    {
        $this->assertNotNull($this->bill);
        $this->bill->setZip('30305');
        $this->assertSame('30305', $this->bill->getZip());
    }

    public function testGetCountry()
    {
        $this->assertNotNull($this->bill);
        $this->assertNull($this->bill->getCountry());
    }

    /**
     * @depends testGetCountry
     */
    public function testSetCountry()
    {
        $this->assertNotNull($this->bill);
        $this->bill->setCountry('US');
        $this->assertSame('US', $this->bill->getCountry());
    }

    public function testGetEmail()
    {
        $this->assertNotNull($this->bill);
        $this->assertNull($this->bill->getEmail());
    }

    /**
     * @depends testGetEmail
     */
    public function testSetEmail()
    {
        $this->assertNotNull($this->bill);
        $this->bill->setEmail('support@bitpay.com');
        $this->assertSame('support@bitpay.com', $this->bill->getEmail());
    }

    public function testGetPhone()
    {
        $this->assertNotNull($this->bill);
        $this->assertNull($this->bill->getPhone());
    }

    /**
     * @depends testGetPhone
     */
    public function testSetPhone()
    {
        $this->assertNotNull($this->bill);
        $this->bill->setPhone('555-555-5555');
        $this->assertSame('555-555-5555', $this->bill->getPhone());
    }

    public function testGetStatus()
    {
        $this->assertNotNull($this->bill);
        $this->assertNull($this->bill->getStatus());
    }

    /**
     * @depends testGetStatus
     */
    public function testSetStatus()
    {
        $this->assertNotNull($this->bill);
        $this->bill->setStatus('unknown');
        $this->assertSame('unknown', $this->bill->getStatus());
    }

    public function testGetShowRate()
    {
        $this->assertNotNull($this->bill);
        $this->assertNull($this->bill->getShowRate());
    }

    /**
     * @depends testGetShowRate
     */
    public function testSetShowRate()
    {
        $this->assertNotNull($this->bill);
        $this->bill->setShowRate('unknown');
        $this->assertSame('unknown', $this->bill->getShowRate());
    }

    public function testGetArchived()
    {
        $this->assertNotNull($this->bill);
        $this->assertFalse($this->bill->getArchived());
    }

    /**
     * @depends testGetArchived
     */
    public function testSetArchived()
    {
        $this->assertNotNull($this->bill);
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
