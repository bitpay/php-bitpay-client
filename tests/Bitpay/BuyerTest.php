<?php

namespace Bitpay;

use Bitpay\Buyer;

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
