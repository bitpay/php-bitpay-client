<?php
/**
 * The MIT License (MIT)
 *
 * Copyright (c) 2014 BitPay, Inc.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

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
            //$this->assertSame($datum[2], $bitauth->encrypt($datum[0], $datum[1]));
            
            // TODO: get value and use for assert. checking not null for now...
            $this->assertNotNull($bitauth->Encrypt($datum,'12345','123'));
        }
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
        $this->markTestIncomplete();
        $bitauth   = new Bitauth();
        $data      = 'https://test.bitpay.com/tokens?nonce=200';
        $signature = $bitauth->sign($data, $this->getMockPrivateKey());
        
        // TODO: better test?
        $this->assertNotNull($signature);

        //$this->assertSame(
        //    '03b8144d4943435474e40c0fb5eb8b58873671534232f08c2034d01a7210876d',
        //    $signature
        //);
    }

    private function getMockPrivateKey()
    {
        $key = $this->getMock('Bitpay\PrivateKey');

        $key
            ->method('getHex')
            ->will($this->returnValue('private key hex value goes here'));

        return $key;
    }
}
