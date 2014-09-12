<?php

namespace Bitpay;

class PrivateKeyTest extends \PHPUnit_Framework_TestCase
{

    public function testCreate()
    {
        $this->assertInstanceOf('Bitpay\PrivateKey', PrivateKey::create());
    }

    public function testGenerate()
    {
        $priKey = new PrivateKey();
        $this->assertNull($priKey->getHex());
        $this->assertNull($priKey->getDec());
        $priKey->generate();
        $this->assertEquals(66, strlen($priKey->getHex()));
        $this->assertGreaterThanOrEqual(76, strlen($priKey->getDec()));
    }

    public function testGetHex()
    {
        $priKey = new PrivateKey();
        $this->assertNull($priKey->getHex());
        $this->assertEquals(0, strlen($priKey->getHex()));
        $priKey->generate();
        $this->assertEquals(66, strlen($priKey->getHex()));
    }

    /**
     * @depends testGetHex
     * @depends testGenerate
     */
    public function testToString()
    {
        $priKey = new PrivateKey();
        // Make sure this is a string
        $this->assertInternalType('string', $priKey->__toString());
        $this->assertSame('', (string) $priKey);
        $priKey->generate();

        // make sure it's still a string after generating hex
        $this->assertInternalType('string', $priKey->__toString());
    }

    public function testGetDec()
    {
        $priKey = new PrivateKey();
        $this->assertNull($priKey->getDec());
        $this->assertEquals(0, strlen($priKey->getDec()));
        $priKey->generate();
        $this->assertGreaterThanOrEqual(76, strlen($priKey->getDec()));
    }

    public function testIsValid()
    {
        $priKey = new PrivateKey();
        $this->assertFalse($priKey->isValid());
        $priKey->generate();
        $this->assertTrue($priKey->isValid());
    }

    public function testSign()
    {
        $priKey = PrivateKey::create()->generate();
        $signature = $priKey->sign('Hello');
    }
}
