<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay\Util;

use Bitpay\Point;

/**
 * @package Bitcore
 */
class UtilTest extends \PHPUnit_Framework_TestCase
{

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

    /**
     * @see https://github.com/bitpay/bitcore/blob/master/test/test.util.js
     */
    public function testTwoSha256()
    {
        $data = array(
            array(
                '907c2bc503ade11cc3b04eb2918b6f547b0630ab569273824748c87ea14b0696526c66ba740200000000fd1f9bdd4ef073c7afc4ae00da8a66f429c917a0081ad1e1dabce28d373eab81d8628de80200000000ad042b5f25efb33beec9f3364e8a9139e8439d9d7e26529c3c30b6c3fd89f8684cfd68ea0200000000599ac2fe02a526ed040000000008535300516352515164370e010000000003006300ab2ec2291fe51c6f',
                '60d8ec2b9241235914528efcc7b32315062d78c8dc12e09bbfdd4cb00563be5b',
            ),
        );

        foreach ($data as $datum) {
            $this->assertSame($datum[1], Util::twoSha256($datum[0]));
        }
    }

    public function testNonce()
    {
        $a = Util::nonce();
        usleep(1);
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

    /**
     * @expectedException Exception
     */
    public function testEncodeException()
    {
        Util::encodeHex(new \StdClass());
    }

    public function testDecodeHex()
    {
        $data = array(
            array('75bcd15', '123456789'),
            array('0x75bcd15', '123456789'),
        );

        foreach ($data as $datum) {
            $this->assertSame(
                $datum[1],
                Util::decodeHex($datum[0])
            );
        }
    }

    /**
     * @expectedException Exception
     */
    public function testDecodeHexException()
    {
        Util::decodeHex(new \StdClass());
    }

    public function testDoubleAndAdd()
    {
        $point = Util::doubleAndAdd('0', new Point(0, 0));
        $this->assertInstanceOf('Bitpay\PointInterface', $point);
        $this->assertTrue($point->isInfinity());
        $point = Util::doubleAndAdd('1', new Point(1, 1));
        $this->assertEquals('1', $point->getX());
        $this->assertEquals('1', $point->getY());
        $point = new Point(
            '0x'.substr(Secp256k1::G, 2, 64),
            '0x'.substr(Secp256k1::G, 66, 64)
        );

        $R = Util::doubleAndAdd(
            '0xb7dafe35d7d1aab78b53982c8ba554584518f86d50af565c98e053613c8f15e0',
            $point
        );
        $this->assertEquals('14976827122927988984909748681266837395089399768482149532452617485742004777865', $R->getX());
        $this->assertEquals('5009713401941157350243425146365130573323232660945282226881202857781593637456', $R->getY());

        $R = Util::doubleAndAdd(
            '0xfd7c6914790d3bbf3184d9830e3f1a327e951e3478dd0b28f0fd3b0e774bbd68',
            $point
        );
        $this->assertEquals('65041784833307054098962518952641430476519680065454324565175938819000678523383', $R->getX());
        $this->assertEquals('53140314933116045874248958072587249546886301333167874306830834776596206062743', $R->getY());
    }

    public function testDecToBin()
    {
        $data = array(
            array('123456789', '101010001011001111011010111'),
            array('0x123456789', '100100011110011010100010110001001'),
        );
        foreach ($data as $datum) {
            $this->assertSame($datum[1], Util::decToBin($datum[0]));
        }
    }

    public function testPointDouble()
    {
        $point = Util::PointDouble(new Point('89565891926547004231252920425935692360644145829622209833684329913297188986597', '-103633689937622365100603176395974509217114616778598935862658712053120463017733'));
        $expectedpoint = new Point("103388573995635080359749164254216598308788835304023601477803095234286494993683", "37057141145242123013015316630864329550140216928701153669873286428255828810018");
        $this->assertEquals($expectedpoint, $point);

        $point = Util::PointDouble(new Point(1, 1));
        $expectedpoint = new Point("28948022309329048855892746252171976963317496166410141009864396001977208667916", "14474011154664524427946373126085988481658748083205070504932198000988604333958");
        $this->assertEquals($expectedpoint, $point);

        $point = Util::PointDouble(new Point(0, 0));
        $expectedpoint = new Point("0", "0");
        $this->assertEquals($expectedpoint, $point);

        $point = Util::PointDouble(new Point("0x79BE667EF9DCBBAC55A06295CE870B07029BFCDB2DCE28D959F2815B16F81798", "0x483ADA7726A3C4655DA4FBFC0E1108A8FD17B448A68554199C47D08FFB10D4B8"));
        $expectedpoint = new Point("89565891926547004231252920425935692360644145829622209833684329913297188986597", "12158399299693830322967808612713398636155367887041628176798871954788371653930");
        $this->assertEquals($expectedpoint, $point);
    }

    public function testPointAdd()
    {
        $point = Util::pointAdd(
           new Point(1, 1),
           new Point(1, 1)
        );
        $expectedpoint = new Point("28948022309329048855892746252171976963317496166410141009864396001977208667916", "14474011154664524427946373126085988481658748083205070504932198000988604333958");
        $this->assertEquals($expectedpoint, $point);

        $point = Util::pointAdd(
           new Point(0, 0),
           new Point(1, 1)
        );
        $expectedpoint = new Point("0", "0");
        $this->assertEquals($expectedpoint, $point);

        $point = Util::pointAdd(
           new Point(1, 0),
           new Point(0, 1)
        );
        $expectedpoint = new Point("0", "115792089237316195423570985008687907853269984665640564039457584007908834671662");
        $this->assertEquals($expectedpoint, $point);
    }

    public function testBinConv()
    {
        $data = array(
            array('7361746f736869', 'satoshi'),
            array('0x7361746f736869', 'satoshi'),
        );
        foreach ($data as $datum) {
            $this->assertSame($datum[1], Util::binConv($datum[0]));
        }
    }

    public function testCheckRequirements()
    {
        $requirements = Util::checkRequirements();

        // PHP Version
        if (!defined('PHP_VERSION_ID')) {
            $version = explode('.', PHP_VERSION);
            define('PHP_VERSION_ID', ($version[0] * 10000 + $version[1] * 100 + $version[2]));
        }
        if (PHP_VERSION_ID >= 50400) {
            $this->assertTrue($requirements['PHP']);
        } else {
            $this->assertTrue(is_string($requirements['PHP']));
        }

        // OpenSSL Extension
        if (extension_loaded('openssl')) {
            $this->assertTrue($requirements['OpenSSL']);
        } else {
            $this->assertTrue(is_string($requirements['OpenSSL']));
        }

        // JSON Extension
        if (extension_loaded('json')) {
            $this->assertTrue($requirements['JSON']);
        } else {
            $this->assertTrue(is_string($requirements['JSON']));
        }

        // cURL Extension
        if (extension_loaded('curl')) {
            $this->assertTrue($requirements['cURL']);
            $curl_version = curl_version();
            $ssl_supported = ($curl_version['features'] & CURL_VERSION_SSL);
            if ($ssl_supported) {
                $this->assertTrue($requirements['cURL.SSL']);
            } else {
                $this->assertTrue(is_string($requirements['cURL.SSL']));
            }
        } else {
            $this->assertTrue(is_string($requirements['cURL']));
            $this->assertTrue(is_string($requirements['cURL']));
        }

        // Math
        if (extension_loaded('bcmath') || extension_loaded('gmp')) {
            $this->assertTrue($requirements['Math']);
        } else {
            $this->assertTrue(is_string($requirements['Math']));
        }
    }

}
