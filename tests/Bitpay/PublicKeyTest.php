<?php

namespace Bitpay;

class PublicKeyTest extends \PHPUnit_Framework_TestCase
{

    public function testCreate()
    {
        $this->assertInstanceOf('Bitpay\PublicKey', PublicKey::create());
    }

    public function testGenerate()
    {
        $pubKey = new PublicKey();
        $pubKey->setPrivateKey(PrivateKey::create()->generate());
        $this->assertNull($pubKey->getHex());
        $this->assertNull($pubKey->getDec());
        $pubKey->generate();
        $this->assertEquals(130, strlen($pubKey->getHex()));
        $this->assertGreaterThanOrEqual(155, strlen($pubKey->getDec()));
    }

    public function testGetHex()
    {
        $pubKey = new PublicKey();
        $pubKey->setPrivateKey(PrivateKey::create()->generate());
        $this->assertNull($pubKey->getHex());
        $pubKey->generate(PrivateKey::create()->generate());
        $this->assertEquals(130, strlen($pubKey->getHex()));
    }

    public function testGetDec()
    {
        $pubKey = new PublicKey();
        $pubKey->setPrivateKey(PrivateKey::create()->generate());
        $this->assertNull($pubKey->getDec());
        $pubKey->generate(PrivateKey::create()->generate());
        $this->assertGreaterThanOrEqual(155, strlen($pubKey->getDec()));
    }

    public function testToString()
    {
        $pubKey = new PublicKey();
        $pubKey->setPrivateKey(PrivateKey::create()->generate());
        $this->assertSame('', (string) $pubKey);
        $pubKey->generate(PrivateKey::create()->generate());

        $compressed = sprintf('02%s', $pubKey->getX());

        $this->assertSame($compressed, (string) $pubKey);
    }

    public function testGetX()
    {
        $pubKey = new PublicKey();
        $pubKey->setPrivateKey(PrivateKey::create()->generate());
        $this->assertNull($pubKey->getX());
        $pubKey->generate(PrivateKey::create()->generate());
        $this->assertEquals(64, strlen($pubKey->getX()));
    }

    public function testGetY()
    {
        $pubKey = new PublicKey();
        $pubKey->setPrivateKey(PrivateKey::create()->generate());
        $this->assertNull($pubKey->getY());
        $pubKey->generate(PrivateKey::create()->generate());
        $this->assertEquals(64, strlen($pubKey->getY()));
    }

    private function getMockPrivateKey()
    {
        return $this->getMockBuilder('Bitpay\PrivateKey')
            ->getMock();
    }
}
