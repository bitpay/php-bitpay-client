<?php

namespace Bitpay\Client;

use Bitpay\Client\Client;

class ClientTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $bitpay = new \Bitpay\Bitpay(
            array(
                'bitpay' => array(
                    'api_key'      => 'test',
                    'network'      => 'testnet',
                    'adapter'      => 'mock',
                    'logger_level' => \Monolog\Logger::DEBUG,
                )
            )
        );
        $this->client = new Client($bitpay->get('logger'));
        $this->client->setContainer($bitpay->getContainer());
    }

    public function testCreateInvoice()
    {
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

        $invoice
            ->method('getBuyer')
            ->willReturn($this->getMockBuyer());

        $invoice
            ->method('getItem')
            ->willReturn($this->getMockItem());

        $invoice
            ->method('getCurrency')
            ->willReturn($this->getMockCurrency());

        $this->client->createInvoice($invoice);
    }

    /**
     * @depends testCreateInvoice
     */
    public function testGetResponse()
    {
        $this->assertNull($this->client->getResponse());

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

        $invoice
            ->method('getBuyer')
            ->willReturn($this->getMockBuyer());

        $invoice
            ->method('getItem')
            ->willReturn($this->getMockItem());

        $invoice
            ->method('getCurrency')
            ->willReturn($this->getMockCurrency());

        $this->client->createInvoice($invoice);

        $this->assertInstanceOf('Bitpay\Client\Response', $this->client->getResponse());
    }

    private function getMockInvoice()
    {
        return $this->getMockBuilder('Bitpay\InvoiceInterface')
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
}
