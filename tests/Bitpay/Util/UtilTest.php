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

namespace Bitpay\Util;

/**
 * @package Bitcore
 */
class UtilTest extends \PHPUnit_Framework_TestCase
{
    public function testSha512()
    {
        $data = array(
            array('test data', '0e1e21ecf105ec853d24d728867ad70613c21663a4693074b2a3619c1bd39d66b588c33723bb466c72424e80e3ca63c249078ab347bab9428500e7ee43059d0d'),
        );

        foreach ($data as $datum) {
            $this->assertSame($datum[1], Util::sha512($datum[0]));
        }
    }

    public function testSha512Hmac()
    {
        $data = array(
            array('data', 'key', '3c5953a18f7303ec653ba170ae334fafa08e3846f2efe317b87efce82376253cb52a8c31ddcde5a3a2eee183c2b34cb91f85e64ddbc325f7692b199473579c58'),
        );

        foreach ($data as $datum) {
            $this->assertSame($datum[2], Util::sha512hmac($datum[0], $datum[1]));
        }
    }

    public function testSha256()
    {
        $data = array(
            array(
                '03d95e184cce34c3cfa58e9a277a09a7c5ed1b2a8134ea1e52887bc66fa3f47071',
                'a5c756101065ac5b8f689139e6d856fa99e54b5000b6428b43729d334cc9277d',
            ),
        );

        foreach ($data as $datum) {
            $this->assertSame($datum[1], Util::sha256($datum[0]));
        }
    }

    /**
     * @see https://github.com/bitpay/bitcore/blob/master/test/test.util.js
     */
    public function testTwoSha256()
    {
        $this->markTestIncomplete();
        $data = array(
            array(
                '907c2bc503ade11cc3b04eb2918b6f547b0630ab569273824748c87ea14b0696526c66ba740200000000fd1f9bdd4ef073c7afc4ae00da8a66f429c917a0081ad1e1dabce28d373eab81d8628de80200000000ad042b5f25efb33beec9f3364e8a9139e8439d9d7e26529c3c30b6c3fd89f8684cfd68ea0200000000599ac2fe02a526ed040000000008535300516352515164370e010000000003006300ab2ec2291fe51c6f',
                '31af167a6cf3f9d5f6875caa4d31704ceb0eba078d132b78dab52c3b8997317e',
            ),
        );

        foreach ($data as $datum) {
            //$this->assertSame($datum[1], Util::twoSha256($datum[0]));
        }
    }

    public function testSha256Ripe160()
    {
        $data = array(
            array(
                '03d95e184cce34c3cfa58e9a277a09a7c5ed1b2a8134ea1e52887bc66fa3f47071',
                'd166a41f27fd4b158f70314e5eee8998bf3d97d5',
            ),
        );

        foreach ($data as $datum) {
            $this->assertSame($datum[1], Util::sha256ripe160($datum[0]));
        }
    }

    public function testRipe160()
    {
        $data = array(
            array('somemessage123', '12fd01a7ec6b9ba23b3a5c16fbfab3ac19624a88'),
            array('', '9c1185a5c5e9fc54612808977ee8f548b2258d31'),
            array('0000', 'ab20e58c9eeb4776e719deff3158e26ca9edb636'),
        );

        foreach ($data as $datum) {
            $this->assertSame($datum[1], Util::ripe160($datum[0]));
        }
    }

    public function testNonce()
    {
        $a = Util::nonce();
        $b = Util::nonce();

        // ensure a < b
        $this->assertGreaterThan($a, $b);
    }

    public function testGuid()
    {
        $guid = Util::guid();

        // ensure proper length
        $this->assertEquals(36, strlen($guid));

        // Make sure in proper format
        $guid = explode('-', $guid);
        $this->assertEquals(8, strlen($guid[0]));
        $this->assertEquals(4, strlen($guid[1]));
        $this->assertEquals(4, strlen($guid[2]));
        $this->assertEquals(4, strlen($guid[3]));
        $this->assertEquals(12, strlen($guid[4]));
    }

    /**
     * @expectedException Exception
     */
    public function testEncodeException()
    {
        Util::encodeHex(new \StdClass());
    }

    public function testEncodeHex()
    {
        $data = array(
            array('123456789', '75bcd15'),
        );

        foreach ($data as $datum) {
            $this->assertSame(
                $datum[1],
                Util::encodeHex($datum[0])
            );
        }
    }

    public function testDecodeHex()
    {
        $data = array(
            array('75bcd15', '123456789'),
        );

        foreach ($data as $datum) {
            $this->assertSame(
                $datum[1],
                Util::decodeHex($datum[0])
            );
        }
    }
}
