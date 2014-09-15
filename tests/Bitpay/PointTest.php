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

    public function testAdd()
    {
        $a = new Point(
            '69b154b42ff9452c31251cb341d7db01ad603dc56d64f9c5fb9e7031b89a241d',
            'eeedc91342b3c8982c1e676435780fe5f9d62f3f692e8d1512485d77fab35997'
        );

        $b = new Point(
            '5a784662a4a20a65bf6aab9ae98a6c068a81c52e4b032c0fb5400c706cfccc56',
            '7f717885be239daadce76b568958305183ad616ff74ed4dc219a74c26d35f839'
        );

        $s = new Point(
            '501e454bf00751f24b1b489aa925215d66af2234e3891c3b21a52bedb3cd711c',
            '008794c1df8131b9ad1e1359965b3f3ee2feef0866be693729772be14be881ab'
        );

        //$sum = $a->add($b);

        //$this->assertSame($sum->getX(), $s->getX());
        //$this->assertSame($sum->getY(), $s->getY());
    }
}
