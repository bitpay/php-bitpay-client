<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay\Util;

use Bitpay\Point;

class GmpTest extends \PHPUnit_Framework_TestCase
{
    public function testDoubleAndAdd()
    {
        $point = Gmp::doubleAndAdd('0', new Point(0, 0));

        $this->assertInstanceOf('Bitpay\PointInterface', $point);
        $this->assertTrue($point->isInfinity());

        $point = Gmp::doubleAndAdd('1', new Point(1, 1));
        $this->assertEquals('1', $point->getX());
        $this->assertEquals('1', $point->getY());
    }

    public function testGmpD2BWithInteger()
    {
        $data = array(
            array('123456789', '101010001011001111011010111'),
            array('0x123456789', '100100011110011010100010110001001'),
        );

        foreach ($data as $datum) {
            $this->assertSame($datum[1], Gmp::gmpD2B($datum[0]));
        }
    }

    public function testPointDouble()
    {
        //$point = Gmp::gmpPointDouble(new Point(1, 1));
        //var_dump($point);
    }

    public function testGmpPointAdd()
    {
        //$point = Gmp::gmpPointAdd(
        //    new Point(1, 1),
        //    new Point(1, 1)
        //);
        //var_dump($point);
    }

    public function testGmpBinconv()
    {
        $data = array(
            array('7361746f736869', 'satoshi'),
            array('0x7361746f736869', 'satoshi'),
        );
        foreach ($data as $datum) {
            $this->assertSame($datum[1], Gmp::gmpBinconv($datum[0]));
        }
    }
}
