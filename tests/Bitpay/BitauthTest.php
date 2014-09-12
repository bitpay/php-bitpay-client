<?php

namespace Bitpay;

/**
 * @package Bitauth
 */
class BitauthTest extends \PHPUnit_Framework_TestCase
{

    public function testGenerateSin()
    {
        $bitauth = new Bitauth();
        $keys    = $bitauth->generateSin();
        $this->assertArrayHasKey('private', $keys);
        $this->assertArrayHasKey('public', $keys);
        $this->assertArrayHasKey('sin', $keys);
        $this->assertInstanceOf('Bitpay\PrivateKey', $keys['private']);
        $this->assertInstanceOf('Bitpay\PublicKey', $keys['public']);
        $this->assertInstanceOf('Bitpay\SinKey', $keys['sin']);
    }

    public function testEncrypt()
    {
        $data = array(
            // password, string, expected
            array('', 'o hai, nsa. how i do teh cryptos?', '3uzFC7hwYwVQ57TfdSFwm4ntSeTXZohFhdZ6nvmeGDWjq9Lu8TENcKtPoRFvtRcHTf'),
            array('s4705hiru13z!', 'o hai, nsa. how i do teh cryptos?', '68whtGQvJEXHGQrY9hPLJRhvzzbhygyG2pbAXkjUcEwYSmEKVLcri9nULpxKoxD3Ac'),
        );

        $bitauth = new Bitauth();

        foreach ($data as $datum) {
            $this->assertSame($datum[2], $bitauth->encrypt($datum[0], $datum[1]));
        }
    }
}
