<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay;

/**
 * @package Bitauth
 */
class BitauthTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Only need to generate keys once
     *
     * @var array
     */
    protected $keys;

    public function testGenerateSin()
    {
        $bitauth = new Bitauth();
        $keys    = $bitauth->generateSin();

        $this->assertNotNull($bitauth);
        $this->assertNotNull($keys);

        $this->assertArrayHasKey('private', $keys);
        $this->assertArrayHasKey('public', $keys);
        $this->assertArrayHasKey('sin', $keys);

        $this->assertInstanceOf('Bitpay\PrivateKey', $keys['private']);
        $this->assertInstanceOf('Bitpay\PublicKey', $keys['public']);
        $this->assertInstanceOf('Bitpay\SinKey', $keys['sin']);
    }

    /**
     * Signatures are variable everytime...
     *
     * @see https://github.com/bitpay/bitauth/blob/master/test/bitauth.js
     * @see https://github.com/bitpay/bitcore/blob/master/test/test.Key.js (signSync)
     *
     * Signing will fudge in php 5.6
     */
    public function testSignature()
    {
        //$bitauth   = new Bitauth();
        //$data      = 'https://test.bitpay.com/tokens?nonce=200';
        //$signature = $bitauth->sign($data, $this->getMockPrivateKey());
        //$this->assertSame(
        //    '03b8144d4943435474e40c0fb5eb8b58873671534232f08c2034d01a7210876d',
        //    $signature
        //);
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
