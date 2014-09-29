<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay;

class BuyerTest extends \PHPUnit_Framework_TestCase
{
    private $user;

    public function setUp()
    {
        $this->user = new Buyer();
    }

    public function testGetPhone()
    {
        $this->assertNotNull($this->user);
        $this->assertNull($this->user->getPhone());
    }

    /**
     * @depends testGetPhone
     */
    public function testSetPhone()
    {
        $this->assertNotNull($this->user);
        $this->user->setPhone('555-555-5555');
        $this->assertSame('555-555-5555', $this->user->getPhone());
    }

    public function testGetEmail()
    {
        $this->assertNotNull($this->user);
        $this->assertNull($this->user->getEmail());
    }

    /**
     * @depends testGetEmail
     */
    public function testSetEmail()
    {
        $this->assertNotNull($this->user);
        $this->user->setEmail('support@bitpay.com');
        $this->assertSame('support@bitpay.com', $this->user->getEmail());
    }

    public function testGetFirstName()
    {
        $this->assertNotNull($this->user);
        $this->assertNull($this->user->getFirstName());
    }

    /**
     * @depends testGetFirstName
     */
    public function testSetFirstName()
    {
        $this->assertNotNull($this->user);
        $this->user->setFirstName('BitPay');
        $this->assertSame('BitPay', $this->user->getFirstName());
    }

    public function testGetLastName()
    {
        $this->assertNotNull($this->user);
        $this->assertNull($this->user->getLastName());
    }

    /**
     * @depends testGetLastName
     */
    public function testSetLastName()
    {
        $this->assertNotNull($this->user);
        $this->user->setLastName('Inc');
        $this->assertSame('Inc', $this->user->getLastName());
    }

    public function testGetAddress()
    {
        $this->assertNotNull($this->user);
        $this->assertNull($this->user->getAddress());
    }

    /**
     * @depends testGetAddress
     */
    public function testSetAddress()
    {
        $this->assertNotNull($this->user);

        $addr = array(
                      '3405 Piedmont Rd NE',
                      'Suite 200',
                     );

        $this->user->setAddress($addr);
        $this->assertSame($addr, $this->user->getAddress());
    }

    public function testGetCity()
    {
        $this->assertNotNull($this->user);
        $this->assertNull($this->user->getCity());
    }

    /**
     * @depends testGetCity
     */
    public function testSetCity()
    {
        $this->assertNotNull($this->user);
        $this->user->setCity('Atlanta');
        $this->assertSame('Atlanta', $this->user->getCity());
    }

    public function testGetState()
    {
        $this->assertNotNull($this->user);
        $this->assertNull($this->user->getState());
    }

    /**
     * @depends testGetState
     */
    public function testSetState()
    {
        $this->assertNotNull($this->user);
        $this->user->setState('GA');
        $this->assertSame('GA', $this->user->getState());
    }

    public function testGetZip()
    {
        $this->assertNotNull($this->user);
        $this->assertNull($this->user->getZip());
    }

    /**
     * @depends testGetZip
     */
    public function testSetZip()
    {
        $this->assertNotNull($this->user);
        $this->user->setZip('30305');
        $this->assertSame('30305', $this->user->getZip());
    }
}
