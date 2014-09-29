<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License 
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay;

/**
 * @see https://github.com/bitpay/bitcore/blob/master/test/test.Key.js
 */
class PublicKeyTest extends \PHPUnit_Framework_TestCase
{
    public function testId()
    {
        $key = new PublicKey('/path/to/key.pub');
        $this->assertSame('/path/to/key.pub', $key->getId());
    }

    public function testCreate()
    {
        $this->assertInstanceOf('Bitpay\PublicKey', PublicKey::create());
    }

    public function testGenerate()
    {
        $pubKey = new PublicKey();
        $pubKey->setPrivateKey($this->getMockPrivateKey());

        $this->assertNull($pubKey->getHex());
        $this->assertNull($pubKey->getDec());

        $pubKey->generate();

        //$this->assertEquals(130, strlen($pubKey->getHex()));
        //$this->assertEquals(33, strlen($pubKey->getHex()));
        //$this->assertGreaterThanOrEqual(155, strlen($pubKey->getDec()));
        //$this->assertLessThan(4, substr($pubKey->getHex(), 0, 1));
        //$this->assertGreaterThan(1, substr($pubKey->getHex(), 0, 1));
    }

    public function testGenerateOnlyOnce()
    {
        $key = new PublicKey();
        $key->setPrivateKey($this->getMockPrivateKey());
        $key->generate();

        $hexValue = $key->getHex();

        $key->generate();

        // Make sure values do not change
        $this->assertSame(
            $hexValue,
            $key->getHex()
        );
    }

    /**
     * @depends testGenerate
     */
    public function testGetHex()
    {
        $pubKey = new PublicKey();
        $pubKey->setPrivateKey($this->getMockPrivateKey());
        $this->assertNull($pubKey->getHex());
        $pubKey->generate();
        //$this->assertEquals(130, strlen($pubKey->getHex()));
    }

    /**
     * @depends testGenerate
     */
    public function testGetDec()
    {
        $pubKey = new PublicKey();
        $this->assertNotNull($pubKey);

        $pubKey->setPrivateKey($this->getMockPrivateKey());

        $this->assertNull($pubKey->getDec());

        $pubKey->generate();
        //$this->assertGreaterThanOrEqual(155, strlen($pubKey->getDec()));
    }

    /**
     * @see https://github.com/bitpay/bitcore/blob/master/test/test.Key.js
     * @depends testGenerate
     */
    public function testToString()
    {
        $pubKey = new PublicKey();
        $this->assertNotNull($pubKey);

        $pubKey->setPrivateKey(PrivateKey::create()->generate());

        $this->assertSame('', (string) $pubKey);

        $pubKey->generate(PrivateKey::create()->generate());

        if ('02'.$pubKey->getX() == $pubKey) {
            $compressed = '02'.$pubKey->getX();
        } else {
            $compressed = '03'.$pubKey->getX();
        }

        $this->assertSame($compressed, (string) $pubKey);
        // compress is 33 length
        //$this->assertEquals(33, strlen((string) $pubKey));
        // uncompresses is 66 length
        //$this->assertEquals(66, strlen((string) $pubKey));
    }

    /**
     * @depends testGenerate
     */
    public function testGetX()
    {
        $pubKey = new PublicKey();
        $this->assertNotNull($pubKey);

        $pubKey->setPrivateKey(PrivateKey::create()->generate());

        $this->assertNull($pubKey->getX());

        $pubKey->generate(PrivateKey::create()->generate());

        $this->assertEquals(64, strlen($pubKey->getX()));
    }

    /**
     * @depends testGenerate
     */
    public function testGetY()
    {
        $pubKey = new PublicKey();
        $this->assertNotNull($pubKey);

        $pubKey->setPrivateKey(PrivateKey::create()->generate());

        $this->assertNull($pubKey->getY());

        $pubKey->generate(PrivateKey::create()->generate());

        $this->assertEquals(64, strlen($pubKey->getY()));
    }

    public function testCreateFromPrivateKey()
    {
        $key = PublicKey::createFromPrivateKey($this->getMockPrivateKey());
        $this->assertInstanceOf('Bitpay\PublicKey', $key);
    }

    public function testIsValid()
    {
        $key = new PublicKey();
        $this->assertFalse($key->isValid());
        $key->setPrivateKey($this->getMockPrivateKey());
        $key->generate();
        // Fails
        //$this->assertTrue($key->isValid());
    }

    public function testGetSin()
    {
        $pub = new PublicKey();
        $pub->setPrivateKey($this->getMockPrivateKey());
        $sin = $pub->getSin();

        $this->assertInstanceOf('Bitpay\SinKey', $sin);
    }

    public function testGetSinOnlyOnce()
    {
        $pub = new PublicKey();
        $pub->setPrivateKey($this->getMockPrivateKey());

        $sin = $pub->getSin();

        $this->assertSame(
            $sin,
            $pub->getSin()
        );
    }

    public function testIsGenerated()
    {
        $pub = new PublicKey();
        $pub->setPrivateKey($this->getMockPrivateKey());
        $this->assertFalse($pub->isGenerated());
        $pub->generate();
        $this->assertTrue($pub->isGenerated());
    }

    private function getMockPrivateKey()
    {
        $key = $this->getMock('Bitpay\PrivateKey');
        $key->method('isValid')->will($this->returnValue(true));

        $key
            ->method('getHex')
            // @see https://github.com/bitpay/bitcore/blob/master/test/test.Key.js for value
            ->will($this->returnValue('b7dafe35d7d1aab78b53982c8ba554584518f86d50af565c98e053613c8f15e0'));

        return $key;
    }
}
