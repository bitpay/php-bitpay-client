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
