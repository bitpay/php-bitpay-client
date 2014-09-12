<?php

namespace Bitpay;

/**
 * @package Bitcore
 */
class SinKeyTest extends \PHPUnit_Framework_TestCase
{

    public function testToString()
    {
        $pubKey = PublicKey::create();
        $pubKey->setPrivateKey(PrivateKey::create()->generate());
        $pubKey->generate();
        $sinKey = new SinKey();
        $sinKey->setPublicKey($pubKey);
        $this->assertSame('', (string) $sinKey);
        $sinKey->generate();
        $this->assertEquals(35, strlen((string) $sinKey));
    }

    /**
     * @expectedException Exception
     */
    public function testGenerateWithException()
    {
        $sinKey = new SinKey();
        $sinKey->setPublicKey(PublicKey::create());
        $sinKey->generate();
    }

    public function testGenerateWithoutException()
    {
        $pubKey = PublicKey::create();
        $pubKey->setPrivateKey(PrivateKey::create()->generate());
        $pubKey->generate();
        $sinKey = new SinKey();
        $sinKey->setPublicKey($pubKey);
        $sinKey->generate();
        $this->assertEquals(35, strlen((string) $sinKey));
    }
}
