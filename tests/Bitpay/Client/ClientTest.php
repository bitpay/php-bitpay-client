<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay\Client;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->client = new Client();
        $this->client->setNetwork(new \Bitpay\Network\Testnet());
        $this->client->setToken($this->getMockToken());
        $this->client->setPublicKey($this->getMockPublicKey());
        $this->client->setPrivateKey($this->getMockPrivateKey());
        $adapter = $this->getMockAdapter();
        $adapter->method('sendRequest')->willReturn($this->getMock('Bitpay\Client\ResponseInterface'));
        $this->client->setAdapter($adapter);
    }

    public function testCreateInvoice()
    {

        $buyer = $this->getMockBuyer();
        $buyer->method('getAddress')->will($this->returnValue(array()));

        $currency = $this->getMockCurrency();
        $currency->method('getCode')->will($this->returnValue('USD'));

        $invoice = new \Bitpay\Invoice();
        $invoice->setOrderId('TEST-01');

        $invoice->setCurrency($currency);

        $item = new \Bitpay\Item();
        $item->setPrice('19.95');
        $invoice->setItem($item);


        $response = $this->getMockResponse();
        $response->method('getBody')->willReturn(file_get_contents(__DIR__ . '/../../DataFixtures/invoice.json'));

        $adapter = $this->getMockAdapter();
        $adapter->method('sendRequest')->willReturn($response);
        $this->client->setAdapter($adapter);

        $invoice = $this->client->createInvoice($invoice);
        $this->assertInstanceOf('Bitpay\InvoiceInterface', $invoice);
        $this->assertEquals('abcdefghijkmnopqrstuvw', $invoice->getId());
        $this->assertEquals('https://test.bitpay.com/invoice?id=abcdefghijkmnopqrstuvw', $invoice->getUrl());
        $this->assertEquals('new', $invoice->getStatus());
        $this->assertEquals('0.0632', $invoice->getBtcPrice());
        $this->assertEquals(19.95, $invoice->getPrice());
        $this->assertEquals(1412594514486, $invoice->getInvoiceTime());
        $this->assertEquals(1412595414486, $invoice->getExpirationTime());
        $this->assertEquals(1412594514518, $invoice->getCurrentTime());
        $this->assertEquals('0.0000', $invoice->getBtcPaid());
        $this->assertEquals(315.7, $invoice->getRate());
        $this->assertEquals(false, $invoice->getExceptionStatus());
    }

    /**
     * @expectedException Exception
     */
    public function testCreateResponseWithException()
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

        $response = $this->getMockResponse();
        $response->method('getBody')->will($this->returnValue('{"error":""}'));

        $adapter = $this->getMockAdapter();
        $adapter->method('sendRequest')->willReturn($response);
        $this->client->setAdapter($adapter);

        $this->client->createInvoice($invoice);
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

        $adapter = $this->getMockAdapter();
        $response = $this->getMockResponse();
        $response->method('getBody')->will($this->returnValue('{"error":""}'));
        $adapter->method('sendRequest')->willReturn($response);
        $this->client->setAdapter($adapter);

        // throws exception
        $this->client->createInvoice($invoice);
    }

    /**
     * @expectedException Exception
     */
    public function testGetCurrenciesWithException()
    {
        $this->client->getCurrencies();
    }

    public function testGetCurrencies()
    {
        $response = $this->getMockResponse();
        $response->method('getBody')->willReturn(file_get_contents(__DIR__ . '/../../DataFixtures/currencies.json'));

        $adapter = $this->getMockAdapter();
        $adapter->method('sendRequest')->willReturn($response);
        $this->client->setAdapter($adapter);

        $currencies = $this->client->getCurrencies();

        $this->assertInternalType('array', $currencies);
        $this->assertGreaterThan(0, count($currencies));
        $this->assertInstanceOf('Bitpay\CurrencyInterface', $currencies[0]);
    }

    public function testCreateToken()
    {
        $response = $this->getMockResponse();
        $response->method('getBody')->willReturn(file_get_contents(__DIR__ . '/../../DataFixtures/tokens.json'));

        $adapter = $this->getMockAdapter();
        $adapter->method('sendRequest')->willReturn($response);
        $this->client->setAdapter($adapter);

        $token = $this->client->createToken();
        $this->assertInstanceOf('Bitpay\TokenInterface', $token);
    }

    /**
     * @expectedException Exception
     */
    public function testCreateTokenWithException()
    {
        $response = $this->getMockResponse();
        $response->method('getBody')->willReturn('{"error":""}');

        $adapter = $this->getMockAdapter();
        $adapter->method('sendRequest')->willReturn($response);
        $this->client->setAdapter($adapter);
        $this->client->createToken(array('id'=>'','pairingCode'=>''));
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

    private function getMockAdapter()
    {
        return $this->getMock('Bitpay\Client\Adapter\AdapterInterface');
    }

    private function getMockPublicKey()
    {
        return $this->getMock('Bitpay\PublicKey');
    }

    private function getMockPrivateKey()
    {
        return $this->getMock('Bitpay\PrivateKey');
    }

    private function getMockResponse()
    {
        return $this->getMock('Bitpay\Client\ResponseInterface');
    }
}
