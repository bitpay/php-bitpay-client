<?php namespace BitPay\Tests;

use \BitPay\BitPay;
use \BitPay\Hash;
use \BitPay\Request\Curl;
use \PHPUnit_Framework_TestCase;
use \Mockery as m;

class BitPayTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        parent::setUp();
    }

    private function mockRequest()
    {
        $response = (object) array(
            'id' => "DGrAEmbsXe9bavBPMJ8kuk",
            'url' => "https://bitpay.com/invoice?id=DGrAEmbsXe9bavBPMJ8kuk",
            'status' => "new",
            'btcPrice' => "0.0495",
            'price' => 10,
            'currency' => "USD",
            'invoiceTime' => 1383265343674,
            'expirationTime' => 1383266243674,
            'currentTime' => 1383265957613
        );

        $mock = m::mock('BitPay\Request\Curl');
        $mock->shouldReceive('get')->andReturn($response);
        $mock->shouldReceive('post')->andReturn($response);

        return $mock;
    }

    public function testConstructWithKey()
    {
        $bitPay = new BitPay(
            new Curl,
            new Hash,
            'KEY'
        );

        $this->assertTrue($bitPay instanceof BitPay);
        $this->assertEquals($bitPay->options['verifyPos'], true);
    }

    public function testConstructWithKeyAndOptions()
    {
        $bitPay = new BitPay(
            new Curl,
            new Hash,
            'KEY',
            array('verifyPos' => false)
        );

        $this->assertTrue($bitPay instanceof BitPay);
        $this->assertEquals($bitPay->options['verifyPos'], false);
    }

    public function testSetOptions()
    {
        $bitPay = new BitPay(
            new Curl,
            new Hash,
            'KEY'
        );

        $this->assertEquals($bitPay->options['verifyPos'], true);

        $bitPay->setOptions(array('verifyPos' => false));

        $this->assertEquals($bitPay->options['verifyPos'], false);
    }

    public function testChainedMethods()
    {
        $bitPay = new BitPay(
            new Curl,
            new Hash
        );

        $this->assertEquals($bitPay->options['verifyPos'], true);

        $bitPay = $bitPay->setApiKey('KEY')->setOptions(array('verifyPos' => false));

        $this->assertTrue($bitPay instanceof BitPay);
        $this->assertEquals($bitPay->options['verifyPos'], false);
    }

    public function testCreateInvoice()
    {

        $bitPay = new BitPay(
            $this->mockRequest(),
            new Hash,
            'KEY'
        );

        $invoice = $bitPay->createInvoice(1, 1, array(), array('price' => 1));
        $this->assertEquals($invoice->id, 'DGrAEmbsXe9bavBPMJ8kuk');
    }

    public function testGetInvoice()
    {

        $bitPay = new BitPay(
            $this->mockRequest(),
            new Hash,
            'KEY'
        );

        $invoice = $bitPay->getInvoice(1);
        $this->assertEquals($invoice->id, 'DGrAEmbsXe9bavBPMJ8kuk');
    }

    public function testVerifyNotification()
    {
        $bitPay = new BitPay(
            new Curl,
            new Hash,
            'KEY'
        );

        $postData = '{"id":"DGrAEmbsXe9bavBPMJ8kuk","url":"https://bitpay.com/invoice?id=' .
                    'DGrAEmbsXe9bavBPMJ8kuk","posData":"{\"posData\":[],\"hash\"' .
                    ':\"y-NseIPPZOp15PzrEmGd4i-2PMXOhjpClOmCefz9cJ4\"}","status":' .
                    '"new","btcPrice":"1.0000","price":1,"currency":"BTC","invoiceTime":1386893490624,' .
                    '"expirationTime":1386894390624,"currentTime":1386893491778}';

        $invoice = $bitPay->verifyNotification($postData);
        $this->assertEquals($invoice->id, 'DGrAEmbsXe9bavBPMJ8kuk');
    }
}
