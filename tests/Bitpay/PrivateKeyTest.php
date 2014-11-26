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
        if (extension_loaded('gmp')) {
            \Bitpay\Math\Math::setEngine(new \Bitpay\Math\GmpEngine());
        } elseif (extension_loaded('bcmath')) {
            \Bitpay\Math\Math::setEngine(new \Bitpay\Math\BcEngine());
        } else {
            \Bitpay\Math\Math::setEngine(new \Bitpay\Math\RpEngine());
        }
        
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

        $this->assertGreaterThanOrEqual(72, strlen($priKey->getDec()));
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

    public function testGetPublicKey()
    {
        $key       = new PrivateKey();
        $publicKey = $key->getPublicKey();

        $this->assertInstanceOf('Bitpay\PublicKey', $publicKey);
    }

    public function testPemDecode()
    {
        $data = '-----BEGIN EC PRIVATE KEY-----' . "\r\n" .
                'MHQCAQEEICg7E4NN53YkaWuAwpoqjfAofjzKI7Jq1f532dX+0O6QoAcGBSuBBAAK' . "\r\n" .
                'oUQDQgAEjZcNa6Kdz6GQwXcUD9iJ+t1tJZCx7hpqBuJV2/IrQBfue8jh8H7Q/4vX' . "\r\n" .
                'fAArmNMaGotTpjdnymWlMfszzXJhlw==' . "\r\n" .
                '-----END EC PRIVATE KEY-----';

        $private_key = '283b13834de77624696b80c29a2a8df0287e3cca23b26ad5fe77d9d5fed0ee90';
        $public_key  = '048d970d6ba29dcfa190c177140fd889fadd6d2590b1ee1a6a06e255dbf22b4017ee7bc8e1f07ed0ff8bd77c002b98d31a1a8b53a63767ca65a531fb33cd726197';

        $pkey = new PrivateKey();
        $this->assertNotNull($pkey);

        $keys = $pkey->pemDecode($data);
        $this->assertNotNull($pkey);

        // Ensure it's an array
        $this->assertInternalType('array', $keys);

        // Ensure the private key matches the expected value
        $this->assertEquals($private_key, $keys['private_key']);

        // Ensure the public key matches the expected value
        $this->assertEquals($public_key, $keys['public_key']);
    }

    public function testPemEncode()
    {
        $data = '-----BEGIN EC PRIVATE KEY-----' . "\r\n" .
                'MHQCAQEEICg7E4NN53YkaWuAwpoqjfAofjzKI7Jq1f532dX+0O6QoAcGBSuBBAAK' . "\r\n" .
                'oUQDQgAEjZcNa6Kdz6GQwXcUD9iJ+t1tJZCx7hpqBuJV2/IrQBfue8jh8H7Q/4vX' . "\r\n" .
                'fAArmNMaGotTpjdnymWlMfszzXJhlw==' . "\r\n" .
                '-----END EC PRIVATE KEY-----';

        $private_key = '283b13834de77624696b80c29a2a8df0287e3cca23b26ad5fe77d9d5fed0ee90';
        $public_key  = '048d970d6ba29dcfa190c177140fd889fadd6d2590b1ee1a6a06e255dbf22b4017ee7bc8e1f07ed0ff8bd77c002b98d31a1a8b53a63767ca65a531fb33cd726197';

        $keypair = array($private_key, $public_key);

        $pkey = new PrivateKey();
        $this->assertNotNull($pkey);

        $pemdata = $pkey->pemEncode($keypair);
        $this->assertNotNull($pemdata);

        // Ensure it's a string
        $this->assertInternalType('string', $pemdata);

        // Ensure the PEM-encoded data matches the expected value
        $this->assertEquals($data, $pemdata);
    }
}
