<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License 
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay;

class PointTest extends \PHPUnit_Framework_TestCase
{
    /**
     */
    public function testConstruct()
    {
        $point = new Point(1, 2);
        $this->assertNotNull($point);
    }

    /**
     * @depends testConstruct
     */
    public function testToString()
    {
        $point = new Point(1, 2);
        $this->assertNotNull($point);

        $this->assertSame('(1, 2)', (string) $point);

        $point = new Point(PointInterface::INFINITY, PointInterface::INFINITY);
        $this->assertSame(PointInterface::INFINITY, (string) $point);
    }

    public function testGetX()
    {
        $point = new Point(1, 2);
        $this->assertNotNull($point);

        $this->assertSame('1', $point->getX());
    }

    public function testGetY()
    {
        $point = new Point(1, 2);
        $this->assertNotNull($point);

        $this->assertSame('2', $point->getY());
    }

    public function testInfinity()
    {
        $point = new Point(1, 2);
        $this->assertFalse($point->isInfinity());

        $point = new Point(PointInterface::INFINITY, PointInterface::INFINITY);
        $this->assertTrue($point->isInfinity());
    }

    public function testSerializeAndUnserialize()
    {
        $point = new Point(1, 2);
        $data  = serialize($point);

        $this->assertSame(
            'C:12:"Bitpay\Point":30:{a:2:{i:0;s:1:"1";i:1;s:1:"2";}}',
            $data
        );

        $pointA = unserialize($data);
        $this->assertInstanceOf('Bitpay\PointInterface', $pointA);
        $this->assertSame('1', $pointA->getX());
        $this->assertSame('2', $pointA->getY());
    }
}
