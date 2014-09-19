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

class CurrencyTest extends \PHPUnit_Framework_TestCase
{
    private $currency;

    public function setUp()
    {
        $this->currency = new Currency();
    }

    public function testGetCode()
    {
        $this->assertNull($this->currency->getCode());
    }

    /**
     * @depends testGetCode
     */
    public function testSetCode()
    {
        $this->currency->setCode('usd');
        $this->assertSame('USD', $this->currency->getCode());
    }

    public function testGetSymbol()
    {
        $this->assertNull($this->currency->getSymbol());
    }

    /**
     * @depends testGetSymbol
     */
    public function testSetSymbol()
    {
        $this->currency->setSymbol('$');
        $this->assertSame('$', $this->currency->getSymbol());
    }

    public function testGetPrecision()
    {
        $this->assertNull($this->currency->getPrecision());
    }

    /**
     * @depends testGetPrecision
     */
    public function testSetPrecision()
    {
        $this->currency->setPrecision(2);
        $this->assertSame(2, $this->currency->getPrecision());
    }

    public function testGetExchangePctFee()
    {
        $this->assertNull($this->currency->getExchangePctFee());
    }

    /**
     * @depends testGetExchangePctFee
     */
    public function testSetExchangePctFee()
    {
        $this->currency->setExchangePctFee('100');
        $this->assertSame('100', $this->currency->getExchangePctFee());
    }

    public function testIsPayoutEnabled()
    {
        $this->assertFalse($this->currency->isPayoutEnabled());
    }

    /**
     * @depends testIsPayoutEnabled
     */
    public function testSetPayoutEnabled()
    {
        $this->currency->setPayoutEnabled(true);
        $this->assertTrue($this->currency->isPayoutEnabled());
    }

    public function testGetName()
    {
        $this->assertNull($this->currency->getName());
    }

    /**
     * @depends testGetName
     */
    public function testSetName()
    {
        $this->currency->setName('US Dollar');
        $this->assertSame('US Dollar', $this->currency->getName());
    }

    public function testGetPluralName()
    {
        $this->assertNull($this->currency->getPluralName());
    }

    /**
     * @depends testGetPluralName
     */
    public function testSetPluralName()
    {
        $this->currency->setPluralName('US Dollars');
        $this->assertSame('US Dollars', $this->currency->getPluralName());
    }

    public function testGetAlts()
    {
        $this->assertNull($this->currency->getAlts());
    }

    /**
     * @depends testGetAlts
     */
    public function testSetAlts()
    {
        $this->currency->setAlts('usd bucks');
        $this->assertSame('usd bucks', $this->currency->getAlts());
    }

    public function testGetPayoutFields()
    {
        $this->assertEmpty($this->currency->getPayoutFields());
    }

    /**
     * @depends testGetPayoutFields
     */
    public function testSetPayoutFields()
    {
        $fields = array(
            'name',
            'account',
            'routing',
            'merchantEIN',
        );
        $this->currency->setPayoutFields($fields);
        $this->assertSame($fields, $this->currency->getPayoutFields());
    }
}
