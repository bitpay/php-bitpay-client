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

namespace Bitpay\Client;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $bitpay = new \Bitpay\Bitpay(__DIR__.'/../../../build/test.yml');
        $this->client = new Client();
        $this->client->setToken($this->getMockToken());
        $this->client->setContainer($bitpay->getContainer());
    }

    public function testCreateInvoice()
    {
        $item = $this->getMockItem();
        $item->method('getPrice')->will($this->returnValue(1));

        $buyer = $this->getMockBuyer();
        $buyer->method('getAddress')->will($this->returnValue(array()));

        $invoice = $this->getMockInvoice();
        $invoice->method('getItem')->willReturn($item);
        $invoice->method('getBuyer')->willReturn($buyer);

        $invoice->method('setId')->will($this->returnSelf());
        $invoice->method('setUrl')->will($this->returnSelf());
        $invoice->method('setStatus')->will($this->returnSelf());
        $invoice->method('setBtcPrice')->will($this->returnSelf());
        $invoice->method('setPrice')->will($this->returnSelf());
        $invoice->method('setInvoiceTime')->will($this->returnSelf());
        $invoice->method('setExpirationTime')->will($this->returnSelf());
        $invoice->method('setCurrentTime')->will($this->returnSelf());
        $invoice->method('setBtcPaid')->will($this->returnSelf());
        $invoice->method('setRate')->will($this->returnSelf());
        $invoice->method('setExceptionStatus')->will($this->returnSelf());
        $invoice->method('getCurrency')->willReturn($this->getMockCurrency());

        $invoice = $this->client->createInvoice($invoice);
        $this->assertInstanceOf('Bitpay\InvoiceInterface', $invoice);
    }

    /**
     * @depends testCreateInvoice
     */
    public function testGetResponse()
    {
        $this->assertNull($this->client->getResponse());
    }

    /**
     * @depends testCreateInvoice
     */
    public function testGetRequest()
    {
        $this->assertNull($this->client->getRequest());
    }

    /**
     * @depends testGetRequest
     * @depends testGetResponse
     * @expectedException Exception
     */
    public function testCreateInvoiceWithError()
    {
        $this->assertNull($this->client->getResponse());
        $this->assertNull($this->client->getRequest());

        $invoice = $this->getMockInvoice();
        $invoice->method('setId')->will($this->returnSelf());
        $invoice->method('setUrl')->will($this->returnSelf());
        $invoice->method('setStatus')->will($this->returnSelf());
        $invoice->method('setBtcPrice')->will($this->returnSelf());
        $invoice->method('setPrice')->will($this->returnSelf());
        $invoice->method('setInvoiceTime')->will($this->returnSelf());
        $invoice->method('setExpirationTime')->will($this->returnSelf());
        $invoice->method('setCurrentTime')->will($this->returnSelf());
        $invoice->method('setBtcPaid')->will($this->returnSelf());
        $invoice->method('setRate')->will($this->returnSelf());
        $invoice->method('setExceptionStatus')->will($this->returnSelf());
        $invoice->method('getCurrency')->willReturn($this->getMockCurrency());
        $invoice->method('getItem')->willReturn($this->getMockItem());
        $invoice->method('getBuyer')->willReturn($this->getMockBuyer());

        // throws exception
        $this->client->createInvoice($invoice);
    }

    /**
     */
    public function testGetCurrenciesWithoutException()
    {
        $currencies = $this->client->getCurrencies();

        $this->assertInternalType('array', $currencies);
        $this->assertGreaterThan(0, count($currencies));
        $this->assertInstanceOf('Bitpay\Currency', $currencies[0]);
    }

    private function getMockInvoice()
    {
        $invoice = $this->getMockBuilder('Bitpay\InvoiceInterface')
            ->setMethods(
                array(
                    'getPrice',
                    'getCurrency',
                    'getItem',
                    'getBuyer',
                    'getTransactionSpeed',
                    'getNotificationEmail',
                    'getNotificationUrl',
                    'getRedirectUrl',
                    'getPosData',
                    'getStatus',
                    'isFullNotifications',
                    'getId',
                    'getUrl',
                    'getBtcPrice',
                    'getInvoiceTime',
                    'getExpirationTime',
                    'getCurrentTime',
                    'getOrderId',
                    'getItemDesc',
                    'getItemCode',
                    'isPhysical',
                    'getBuyerName',
                    'getBuyerAddress1',
                    'getBuyerAddress2',
                    'getBuyerCity',
                    'getBuyerState',
                    'getBuyerZip',
                    'getBuyerCountry',
                    'getBuyerEmail',
                    'getBuyerPhone',
                    'getExceptionStatus',
                    'getBtcPaid',
                    'getRate',
                    'setId',
                    'setUrl',
                    'setStatus',
                    'setBtcPrice',
                    'setPrice',
                    'setInvoiceTime',
                    'setExpirationTime',
                    'setCurrentTime',
                    'setBtcPaid',
                    'setRate',
                    'setExceptionStatus',
                )
            )
            ->getMock();

        return $invoice;
    }

    private function getMockBuyer()
    {
        return $this->getMockBuilder('Bitpay\BuyerInterface')
            ->setMethods(
                array(
                    'getPhone',
                    'getEmail',
                    'getFirstName',
                    'getLastName',
                    'getAddress',
                    'getCity',
                    'getState',
                    'getZip',
                    'getCountry',
                )
            )
            ->getMock();
    }

    private function getMockItem()
    {
        return $this->getMockBuilder('Bitpay\ItemInterface')
            ->setMethods(
                array(
                    'getCode',
                    'getDescription',
                    'getPrice',
                    'getQuantity',
                    'isPhysical',
                )
            )
            ->getMock();
    }

    private function getMockCurrency()
    {
        return $this->getMockBuilder('Bitpay\CurrencyInterface')
            ->setMethods(
                array(
                    'getCode',
                    'getSymbol',
                    'getPrecision',
                    'getExchangePctFee',
                    'isPayoutEnabled',
                    'getName',
                    'getPluralName',
                    'getAlts',
                    'getPayoutFields',
                )
            )
            ->getMock();
    }

    private function getMockToken()
    {
        return $this->getMock('Bitpay\TokenInterface');
    }
}
