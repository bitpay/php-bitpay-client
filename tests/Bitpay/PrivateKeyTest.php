<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License 
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay;

class PrivateKeyTest extends \PHPUnit_Framework_TestCase
{
    public function testId()
    {
        $key = new PrivateKey('/path/to/key.pri');
        $this->assertSame('/path/to/key.pri', $key->getId());
    }

    public function testCreate()
    {
        $this->assertInstanceOf('Bitpay\PrivateKey', PrivateKey::create());
    }

    public function testGenerate()
    {
        $priKey = new PrivateKey();
        $this->assertNotNull($priKey);

        $this->assertNull($priKey->getHex());
        $this->assertNull($priKey->getDec());

        $priKey->generate();

        $this->assertEquals(64, strlen($priKey->getHex()));
        $this->assertGreaterThanOrEqual(72, strlen($priKey->getDec()));
    }

    public function testGenerateCannotHappenTwice()
    {
        $privateKey = new PrivateKey();
        $privateKey->generate();
        $hex = $privateKey->getHex();
        $privateKey->generate();
        $this->assertSame($hex, $privateKey->getHex());
    }

    public function testGetHex()
    {
        $priKey = new PrivateKey();
        $this->assertNotNull($priKey);

        $this->assertNull($priKey->getHex());

        $this->assertEquals(0, strlen($priKey->getHex()));

        $priKey->generate();

        $this->assertEquals(64, strlen($priKey->getHex()));
    }

    /**
     * @depends testGetHex
     * @depends testGenerate
     */
    public function testToString()
    {
        $priKey = new PrivateKey();
        $this->assertNotNull($priKey);

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
        $this->assertNotNull($priKey);

        $this->assertNull($priKey->getDec());

        $this->assertEquals(0, strlen($priKey->getDec()));

        $priKey->generate();

        $this->assertGreaterThanOrEqual(76, strlen($priKey->getDec()));
    }

    public function testIsValid()
    {
        $priKey = new PrivateKey();
        $this->assertNotNull($priKey);

        $this->assertFalse($priKey->isValid());

        $priKey->generate();

        $this->assertTrue($priKey->isValid());
    }

    public function testSign()
    {
        $priKey = new PrivateKey();
        $priKey->generate();

        // Make sure not exceptions are thrown
        $priKey->sign('BitPay');
    }

    public function testHasValidHex()
    {
        $priKey = new PrivateKey();
        $this->assertNotNull($priKey);

        $this->assertFalse($priKey->hasValidHex());

        $priKey->generate();

        $this->assertTrue($priKey->hasValidHex());
    }

    public function testHasValidDec()
    {
        $priKey = new PrivateKey();
        $this->assertNotNull($priKey);

        $this->assertFalse($priKey->hasValidDec());

        $priKey->generate();

        $this->assertTrue($priKey->hasValidDec());
    }

    /**
     * @see https://github.com/bitpay/bitcore/blob/master/test/test.PrivateKey.js
     */
    public function testCreateFromHex()
    {
        //$hex      = 'b9f4892c9e8282028fea1d2667c4dc5213564d41fc5783896a0d843fc15089f3';
        //$expected = 'cTpB4YiyKiBcPxnefsDpbnDxFDffjqJob8wGCEDXxgQ7zQoMXJdH';
        //$key = PrivateKey::createFromHex($hex);
        //$this->assertSame($hex, (string) $key);
    }

    public function testGetPublicKey()
    {
        $key       = new PrivateKey();
        $publicKey = $key->getPublicKey();

        $this->assertInstanceOf('Bitpay\PublicKey', $publicKey);
    }
}
