<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License 
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay;

/**
 * @package Bitcore
 */
class SinKeyTest extends \PHPUnit_Framework_TestCase
{
    public function testToString()
    {
        $pubKey = PublicKey::create();
        $this->assertNotNull($pubKey);

        $pubKey->setPrivateKey(PrivateKey::create()->generate());

        $pubKey->generate();

        $sinKey = new SinKey();
        $this->assertNotNull($sinKey);

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
        $this->assertNotNull($sinKey);
        $sinKey->generate();
    }

    /**
     * @expectedException Exception
     */
    public function testGenerateWithoutPublicKey()
    {
        $sinKey = new SinKey();
        $this->assertNotNull($sinKey);

        $sinKey->generate();
    }

    public function testGenerateWithoutException()
    {
        $pubKey = PublicKey::create();
        $this->assertNotNull($pubKey);

        $pubKey->setPrivateKey(PrivateKey::create()->generate());

        $pubKey->generate();

        $sinKey = new SinKey();
        $this->assertNotNull($sinKey);

        $sinKey->setPublicKey($pubKey);

        $sinKey->generate();

        $this->assertEquals(35, strlen((string) $sinKey));
    }

    /**
     * @depnds testGenerateWithoutException
     */
    public function testIsValid()
    {
        $sinKey = new SinKey();
        $this->assertNotNull($sinKey);

        $this->assertFalse($sinKey->isValid());

        $pubKey = PublicKey::create();
        $this->assertNotNull($pubKey);

        $pubKey->setPrivateKey(PrivateKey::create()->generate());

        $pubKey->generate();

        $sinKey->setPublicKey($pubKey);

        $sinKey->generate();

        $this->assertTrue($sinKey->isValid());
    }
}
