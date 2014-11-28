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

    private $hexKeys = array(
        array(
            'private' => 'b7dafe35d7d1aab78b53982c8ba554584518f86d50af565c98e053613c8f15e0',
            'public' => '02211c9570d24ba84a3ee31c8a08e93a6756b3f3beac76a4ab8d9748ca78203389'
        ),
        array(
            'private' => '876156ccb16bb1760ddda6ad3e561c026fc0d679ad7860b71dd11c30e42f6589',
            'public' => '0394615227fd5ff4d4dfac88cf148e43d35a7a059788dd2479f60cea807b09d0c2'
        ),
        array(
            'private' => 'c6d202e281efee7a77934d1bbc8c958823a784899533c2bef087eb219856e168',
            'public' => '02513706c80e2d06338726ba345dc2ea1b598a4d783c76cbd25844ae3531e13045'
        ),
    );

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
        foreach($this->hexKeys as $hexKey) {
            $pubKey = new PublicKey();
            $pubKey->setPrivateKey($this->getMockPrivateKey($hexKey['private']));
            $pubKey->generate();
            $this->assertEquals($hexKey['public'], (string) $pubKey);
        }
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

        $this->assertEquals(66, strlen((string) $pubKey));
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
        $this->assertTrue($key->isValid());
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

    private function getMockPrivateKey($hex = null)
    {
        $hex = ($hex === null) ? $this->hexKeys[0]['private'] : $hex;
        $key = $this->getMock('Bitpay\PrivateKey');
        $key->method('isValid')->will($this->returnValue(true));

        $key
            ->method('getHex')
            ->will($this->returnValue($hex));
            
        return $key;
    }

}
