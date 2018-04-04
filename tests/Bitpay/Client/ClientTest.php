<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay\Client;

date_default_timezone_set('UTC');

class ChildOfClient extends Client
{
    public function checkPriceAndCurrency($price, $currency) {
        return parent::checkPriceAndCurrency($price, $currency);
    }
}

class ClientTest extends \PHPUnit_Framework_TestCase
{
    protected $client;

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

    public function testCheckPriceAndCurrency() {
        $client = new ChildOfClient();
        $res = $client->checkPriceAndCurrency(.999999, 'BTC');
        $this->assertNull($res);
        $res = $client->checkPriceAndCurrency(1000, 'USD');
        $this->assertNull($res);
        $res = $client->checkPriceAndCurrency(0, 'USD');
        $this->assertNull($res);
        $res = $client->checkPriceAndCurrency(.01, 'USD');
        $this->assertNull($res);
        $res = $client->checkPriceAndCurrency(99, 'USD');
        $this->assertNull($res);
        $res = $client->checkPriceAndCurrency(100.9, 'USD');
        $this->assertNull($res);
    }

    /**
     * @expectedException \Exception
     */
    public function testCheckPriceAndCurrencyWithException() {
        $client = new ChildOfClient();
        $res = $client->checkPriceAndCurrency(.991, 'ABC');
    }

    /**
     * @expectedException \Exception
     */
    public function testCreatePayoutWithException()
    {
        $response = $this->getMockResponse();
        $response->method('getBody')->willReturn('{"error":"Error with request"}');

        $adapter = $this->getMockAdapter();
        $adapter->method('sendRequest')->willReturn($response);
        $this->client->setAdapter($adapter);

        $currency = $this->getMockCurrency();
        $currency->method('getCode')->will($this->returnValue('USD'));

        $token = $this->getMockToken();

        $payout = new \Bitpay\Payout();
        $payout
            ->setCurrency($currency)
            ->setEffectiveDate("1415853007000")
            ->setPricingMethod('bitcoinbestbuy')
            ->setToken($token);

        $this->client->createPayout($payout);
    }

    public function testCreatePayout()
    {
        $currency = $this->getMockCurrency();
        $currency->method('getCode')->will($this->returnValue('USD'));

        $token = $this->getMockToken();

        $payout = new \Bitpay\Payout();
        $payout
            ->setCurrency($currency)
            ->setEffectiveDate("1415853007000")
            ->setPricingMethod('bitcoinbestbuy')
            ->setNotificationUrl('https://bitpay.com')
            ->setNotificationEmail('support@bitpay.com')
            ->setPricingMethod('bitcoinbestbuy')
            ->setReference('your reference, can be json')
            ->setAmount(5625)
            ->setToken($token);

        $btc_amounts = array(
            \Bitpay\PayoutInstruction::STATUS_UNPAID => null,
            \Bitpay\PayoutInstruction::STATUS_PAID => '0'
        );
        $instruction0 = new \Bitpay\PayoutInstruction();
        $instruction0
            ->setId('Sra19AFU57Rx53rKQbbRKZ')
            ->setAmount(1875)
            ->setLabel('2')
            ->setStatus(\Bitpay\PayoutInstruction::STATUS_UNPAID)
            ->setBtc($btc_amounts)
            ->setAddress('mzzsJ8G9KBmHPPVYaMxpYRetWRRec78FvF');

        $instruction1 = new \Bitpay\PayoutInstruction();
        $instruction1
            ->setId('5SCdU1xNsEwrUFqKChYuAR')
            ->setAmount(1875)
            ->setLabel('3')
            ->setStatus(\Bitpay\PayoutInstruction::STATUS_UNPAID)
            ->setBtc($btc_amounts)
            ->setAddress('mre3amN8KCFuy7gWCjhFXjuqkmoJMkd2gx');

        $instruction2 = new \Bitpay\PayoutInstruction();
        $instruction2
            ->setId('5cHNbnmNuo8gRawnrFZsPy')
            ->setAmount(1875)
            ->setLabel('4')
            ->setStatus(\Bitpay\PayoutInstruction::STATUS_UNPAID)
            ->setBtc($btc_amounts)
            ->setAddress('mre3amN8KCFuy7gWCjhFXjuqkmoJMkd2gx');

        $payout
            ->addInstruction($instruction0)
            ->addInstruction($instruction1)
            ->addInstruction($instruction2);

        $response = $this->getMockResponse();
        $response->method('getBody')->willReturn(file_get_contents(__DIR__ . '/../../DataFixtures/payouts/7m7hSF3ws1LhnWUf17CXsJ.json'));

        $adapter = $this->getMockAdapter();
        $adapter->method('sendRequest')->willReturn($response);
        $this->client->setAdapter($adapter);

        $payout = $this->client->createPayout($payout);
        $this->assertInstanceOf('Bitpay\PayoutInterface', $payout);
        $this->assertEquals('7m7hSF3ws1LhnWUf17CXsJ', $payout->getId());
        $this->assertEquals('Lwbnf9XAPCxDmy8wsRH3ct', $payout->getAccountId());
        $this->assertEquals(\Bitpay\Payout::STATUS_NEW, $payout->getStatus());
        $this->assertEquals(5625, $payout->getAmount());
        $this->assertEquals(null, $payout->getRate());
        $this->assertEquals(null, $payout->getBtcAmount());
        $this->assertEquals('bitcoinbestbuy', $payout->getPricingMethod());
        $this->assertEquals('your reference, can be json', $payout->getReference());
        $this->assertEquals('1415853007000', $payout->getEffectiveDate());
        $this->assertEquals('https://bitpay.com', $payout->getNotificationUrl());
        $this->assertEquals('support@bitpay.com', $payout->getNotificationEmail());
        $this->assertEquals('8mZ37Gt91Wr7GXGPnB9zj1zwTcLGweRDka4axVBPi9Uxiiv7zZWvEKSgmFddQZA1Jy', $payout->getResponseToken());
        $instructions = $payout->getInstructions();
        $this->assertSame($instruction0, $instructions[0]);
        $this->assertSame($instruction1, $instructions[1]);
        $this->assertSame($instruction2, $instructions[2]);
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
        $this->assertEquals(19.95, $invoice->getPrice());
        $this->assertInstanceOf('DateTime', $invoice->getInvoiceTime());
        $this->assertInstanceOf('DateTime', $invoice->getExpirationTime());
        $this->assertInstanceOf('DateTime', $invoice->getCurrentTime());
        $this->assertEquals(false, $invoice->getExceptionStatus());
        $this->assertEquals('abcdefghijklmno', $invoice->getToken()->getToken());
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
        $invoice->method('setPrice')->will($this->returnSelf());
        $invoice->method('setInvoiceTime')->will($this->returnSelf());
        $invoice->method('setExpirationTime')->will($this->returnSelf());
        $invoice->method('setCurrentTime')->will($this->returnSelf());
        $invoice->method('setExceptionStatus')->will($this->returnSelf());
        $invoice->method('getCurrency')->willReturn($this->getMockCurrency());

        $response = $this->getMockResponse();
        $response->method('getBody')->will($this->returnValue('{"error":"Some error message"}'));

        $adapter = $this->getMockAdapter();
        $adapter->method('sendRequest')->willReturn($response);
        $this->client->setAdapter($adapter);

        $this->client->createInvoice($invoice);
    }

    /**
     *  @expectedException Exception
     */
    public function testCreateInvoiceWithTooMuchPrecisionForAnythingButBitcoin()
    {
        $item = $this->getMockItem();
        $currency = $this->getMockCurrency();
        $currency->method('getCode')->will($this->returnValue("USD"));
        $item->method('getPrice')->will($this->returnValue(9.999));

        $buyer = $this->getMockBuyer();
        $buyer->method('getAddress')->will($this->returnValue(array()));

        $invoice = $this->getMockInvoice();
        $invoice->method('getItem')->willReturn($item);
        $invoice->method('getBuyer')->willReturn($buyer);
        $invoice->method('setId')->will($this->returnSelf());
        $invoice->method('setUrl')->will($this->returnSelf());
        $invoice->method('setStatus')->will($this->returnSelf());
        $invoice->method('setPrice')->will($this->returnSelf());
        $invoice->method('setInvoiceTime')->will($this->returnSelf());
        $invoice->method('setExpirationTime')->will($this->returnSelf());
        $invoice->method('setCurrentTime')->will($this->returnSelf());
        $invoice->method('setExceptionStatus')->will($this->returnSelf());
        $invoice->method('getCurrency')->willReturn($currency);

        $this->client->createInvoice($invoice);
    }


    /**
     *  @expectedException Exception
     */
    public function testCreateInvoiceWithTooMuchPrecisionEvenForBitcoin()
    {
        $item = $this->getMockItem();
        $currency = $this->getMockCurrency();
        $currency->method('getCode')->will($this->returnValue("BTC"));
        $item->method('getPrice')->will($this->returnValue(.9999999));

        $buyer = $this->getMockBuyer();
        $buyer->method('getAddress')->will($this->returnValue(array()));

        $invoice = $this->getMockInvoice();
        $invoice->method('getItem')->willReturn($item);
        $invoice->method('getBuyer')->willReturn($buyer);
        $invoice->method('setId')->will($this->returnSelf());
        $invoice->method('setUrl')->will($this->returnSelf());
        $invoice->method('setStatus')->will($this->returnSelf());
        $invoice->method('setPrice')->will($this->returnSelf());
        $invoice->method('setInvoiceTime')->will($this->returnSelf());
        $invoice->method('setExpirationTime')->will($this->returnSelf());
        $invoice->method('setCurrentTime')->will($this->returnSelf());
        $invoice->method('setExceptionStatus')->will($this->returnSelf());
        $invoice->method('getCurrency')->willReturn($currency);

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
        $invoice->method('setPrice')->will($this->returnSelf());
        $invoice->method('setInvoiceTime')->will($this->returnSelf());
        $invoice->method('setExpirationTime')->will($this->returnSelf());
        $invoice->method('setCurrentTime')->will($this->returnSelf());
        $invoice->method('setExceptionStatus')->will($this->returnSelf());
        $invoice->method('getCurrency')->willReturn($this->getMockCurrency());
        $invoice->method('getItem')->willReturn($this->getMockItem());
        $invoice->method('getBuyer')->willReturn($this->getMockBuyer());

        $adapter = $this->getMockAdapter();
        $response = $this->getMockResponse();
        $response->method('getBody')->will($this->returnValue('{"error":"Some error message"}'));
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

    public function testGetPayouts()
    {
        $response = $this->getMockResponse();
        $response->method('getBody')->willReturn(file_get_contents(__DIR__ . '/../../DataFixtures/payouts/getpayouts.json'));

        $adapter = $this->getMockAdapter();
        $adapter->method('sendRequest')->willReturn($response);
        $this->client->setAdapter($adapter);

        $payouts = $this->client->getPayouts();
        $this->assertInternalType('array', $payouts);
        $this->assertInstanceOf('Bitpay\PayoutInterface', $payouts[0]);

    }

    /**
     * @expectedException \Exception
     */
    public function testGetPayoutsWithException()
    {
        $response = $this->getMockResponse();
        $response->method('getBody')->willReturn('{"error":"Some error message"}');

        $adapter = $this->getMockAdapter();
        $adapter->method('sendRequest')->willReturn($response);
        $this->client->setAdapter($adapter);

        $payouts = $this->client->getPayouts();

    }

    public function testGetTokens()
    {
        $response = $this->getMockResponse();
        $response->method('getBody')->willReturn(file_get_contents(__DIR__ . '/../../DataFixtures/with_tokens.json'));

        $adapter = $this->getMockAdapter();
        $adapter->method('sendRequest')->willReturn($response);
        $this->client->setAdapter($adapter);

        $tokens = $this->client->getTokens();
        $this->assertInternalType('array', $tokens);
        $this->assertSame('39zPuHaBbO8VMZe8Bdr9RjmRY6pHT7Gs3ifcbKM6PYSg2', $tokens['payroll']->getToken());
        $this->assertSame('payroll', $tokens['payroll']->getFacade());

        $this->assertSame('5QziWnr75x7c4B9DdJ5QUo', $tokens['payroll/payoutRequest']->getToken());
        $this->assertSame('payroll/payoutRequest', $tokens['payroll/payoutRequest']->getFacade());
    }

    /**
     * @expectedException \Exception
     */
    public function testGetTokensWithException()
    {
        $response = $this->getMockResponse();
        $response->method('getBody')->willReturn('{"error":""}');

        $adapter = $this->getMockAdapter();
        $adapter->method('sendRequest')->willReturn($response);
        $this->client->setAdapter($adapter);

        $tokens = $this->client->getTokens();
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

        $response = $this->getMockResponse();
        $response->method('getBody')->willReturn(file_get_contents(__DIR__ . '/../../DataFixtures/tokens_pairing.json'));

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

    public function testGetInvoice()
    {
        $response = $this->getMockResponse();
        $response->method('getBody')->willReturn(file_get_contents(__DIR__ . '/../../DataFixtures/invoices/5NxFkXcJbCSivtQRJa4kHP.json'));

        $adapter = $this->getMockAdapter();
        $adapter->method('sendRequest')->willReturn($response);
        $this->client->setAdapter($adapter);
        $token = new \Bitpay\Token();
        $token->setToken('asdfsds');

        // No token/public facade
        $invoice = $this->client->getInvoice('5NxFkXcJbCSivtQRJa4kHP');
        $this->assertSame('invoices/5NxFkXcJbCSivtQRJa4kHP', $this->client->getRequest()->getPath());
        $this->assertInstanceOf('Bitpay\InvoiceInterface', $invoice);

        // pos token/public facade
        $this->client->setToken($token->setFacade('pos'));
        $invoice = $this->client->getInvoice('5NxFkXcJbCSivtQRJa4kHP');
        $this->assertSame('invoices/5NxFkXcJbCSivtQRJa4kHP', $this->client->getRequest()->getPath());
        $this->assertInstanceOf('Bitpay\InvoiceInterface', $invoice);

        // merchant token/merchant facade
        $this->client->setToken($token->setFacade('merchant'));
        $invoice = $this->client->getInvoice('5NxFkXcJbCSivtQRJa4kHP');
        $this->assertSame('invoices/5NxFkXcJbCSivtQRJa4kHP?token=asdfsds', $this->client->getRequest()->getPath());
        $this->assertInstanceOf('Bitpay\InvoiceInterface', $invoice);
    }

    /**
     * @expectedException Exception
     */
    public function testGetInvoiceException()
    {
        $response = $this->getMockResponse();
        $response->method('getBody')->willReturn('{"error":"Object not found"}');
        $adapter = $this->getMockAdapter();
        $adapter->method('sendRequest')->willReturn($response);
        $this->client->setAdapter($adapter);
        $this->client->getInvoice('5NxFkXcJbCSivtQRJa4kHP');
    }

    public function testGetPayout()
    {
        $response = $this->getMockResponse();
        $response->method('getBody')->willReturn(file_get_contents(__DIR__ . '/../../DataFixtures/payouts/complete.json'));

        $adapter = $this->getMockAdapter();
        $adapter->method('sendRequest')->willReturn($response);
        $this->client->setAdapter($adapter);
        $payout = $this->client->getPayout('7m7hSF3ws1LhnWUf17CXsJ');
        $this->assertInstanceOf('Bitpay\PayoutInterface', $payout);
        $this->assertSame($payout->getId(), '7AboMecD4jSMXbH7DaJJvm');
        $this->assertSame($payout->getAccountId(), 'Lwbnf9XAPCxDmy8wsRH3ct');
        $this->assertSame($payout->getStatus(), 'complete');
        $this->assertSame($payout->getRate(), 352.23);
        $this->assertSame($payout->getAmount(), 5625);
        $this->assertSame($payout->getBtcAmount(), 15.9696);
        $this->assertSame($payout->getCurrency()->getCode(), 'USD');
    }

    /**
     * @expectedException Exception
     */
    public function testGetPayoutException()
    {
        $response = $this->getMockResponse();
        $response->method('getBody')->willReturn('{"error":"Object not found"}');

        $adapter = $this->getMockAdapter();
        $adapter->method('sendRequest')->willReturn($response);
        $this->client->setAdapter($adapter);
        $this->client->getPayout('5NxFkXcJbCSivtQRJa4kHP');
    }

    public function testDeletePayout()
    {
        // Set up using getPayout
        $response = $this->getMockResponse();
        $response->method('getBody')->willReturn(file_get_contents(__DIR__ . '/../../DataFixtures/payouts/7m7hSF3ws1LhnWUf17CXsJ.json'));

        $adapter = $this->getMockAdapter();
        $adapter->method('sendRequest')->willReturn($response);
        $this->client->setAdapter($adapter);

        $payout = $this->client->getPayout('7m7hSF3ws1LhnWUf17CXsJ');


        // Test deletePayout
        $response = $this->getMockResponse();
        $response->method('getBody')->willReturn(file_get_contents(__DIR__ . '/../../DataFixtures/payouts/cancelled.json'));

        $adapter = $this->getMockAdapter();
        $adapter->method('sendRequest')->willReturn($response);
        $this->client->setAdapter($adapter);

        $payout = $this->client->deletePayout($payout);

        $this->assertSame($payout->getStatus(), \Bitpay\Payout::STATUS_CANCELLED);
    }

    /**
     * @expectedException \Exception
     */
    public function testDeletePayoutWithException()
    {
        // Setup using getPayout
        $response = $this->getMockResponse();
        $response->method('getBody')->willReturn(file_get_contents(__DIR__ . '/../../DataFixtures/payouts/7m7hSF3ws1LhnWUf17CXsJ.json'));

        $adapter = $this->getMockAdapter();
        $adapter->method('sendRequest')->willReturn($response);
        $this->client->setAdapter($adapter);

        $payout = $this->client->getPayout('7m7hSF3ws1LhnWUf17CXsJ');

        // Test with exception

        $response = $this->getMockResponse();
        $response->method('getBody')->willReturn('{"error":"Object not found"}');

        $adapter = $this->getMockAdapter();
        $adapter->method('sendRequest')->willReturn($response);
        $this->client->setAdapter($adapter);

        $payout = $this->client->deletePayout($payout);
        $this->assertSame($payout->getStatus(), \Bitpay\Payout::STATUS_CANCELLED);
    }


    private function getMockInvoice()
    {
        $invoice = $this->getMockBuilder('Bitpay\InvoiceInterface')
            ->setMethods(
                array(
                    'getPrice', 'getCurrency', 'getItem', 'getBuyer', 'getTransactionSpeed',
                    'getNotificationEmail', 'getNotificationUrl', 'getRedirectUrl', 'getPosData', 'getStatus',
                    'isFullNotifications', 'getId', 'getUrl', 'getInvoiceTime',
                    'getExpirationTime', 'getCurrentTime', 'getOrderId', 'getItemDesc', 'getItemCode',
                    'isPhysical', 'getBuyerName', 'getBuyerAddress1', 'getBuyerAddress2', 'getBuyerCity',
                    'getBuyerState', 'getBuyerZip', 'getBuyerCountry', 'getBuyerEmail', 'getBuyerPhone',
                    'getExceptionStatus', 'getToken', 'getRefundAddresses',
                    'getExchangeRates', 'getPaymentSubtotals', 'getPaymentTotals', 'getTransactionCurrency', 'getAmountPaid',
                    'setId', 'setUrl', 'setStatus', 'setInvoiceTime', 'setExpirationTime',
                    'setCurrentTime', 'setToken', 'setExceptionStatus', 'isExtendedNotifications',
                    'setExchangeRates', 'setPaymentSubtotals', 'setPaymentTotals', 'setTransactionCurrency', 'setAmountPaid'
                )
            )
            ->getMock();

        return $invoice;
    }

    private function getMockPayout()
    {
        $invoice = $this->getMockBuilder('Bitpay\PayoutInterface')
            ->setMethods(
                array(
                    'getId',
                    'setId',
                    'getAccountId',
                    'setAccountId',
                    'getAmount',
                    'getCurrency',
                    'setCurrency',
                    'getEffectiveDate',
                    'setEffectiveDate',
                    'getRate',
                    'setRate',
                    'getRequestDate',
                    'setRequestDate',
                    'getInstructions',
                    'addInstruction',
                    'updateInstruction',
                    'getStatus',
                    'setStatus',
                    'getToken',
                    'setToken',
                    'getResponseToken',
                    'setResponseToken',
                    'getPricingMethod',
                    'setPricingMethod',
                    'getReference',
                    'setReference',
                    'getNotificationEmail',
                    'setNotificationEmail',
                    'getNotificationUrl',
                    'setNotificationUrl',
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
                    'getNotify'
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
