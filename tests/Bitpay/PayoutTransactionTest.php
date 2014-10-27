<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay;

class PayoutTransactionTest extends \PHPUnit_Framework_TestCase
{
    private $tx;

    public function setUp()
    {
        $this->tx = new PayoutTransaction();
    }

    public function testGetTransactionId()
    {
        $this->assertNotNull($this->tx);
        $this->assertNull($this->tx->getTransactionId());
    }

    /**
     * @depends testGetTransactionId
     */
    public function testSetTransactionId()
    {
        $this->tx->setTransactionId('00000000b456d28e2769762d1de5c8c668282c5dca566c226c455da4bd20aa78');
        $this->assertNotNull($this->tx->getTransactionId());
        $this->assertSame($this->tx->getTransactionId(), '00000000b456d28e2769762d1de5c8c668282c5dca566c226c455da4bd20aa78');
    }

    public function testGetAmount()
    {
        $this->assertNotNull($this->tx);
        $this->assertNull($this->tx->getAmount());
    }

    /**
     * @depends testGetAmount
     */
    public function testSetAmount()
    {
        $this->tx->setAmount(999.99);
        $this->assertNotNull($this->tx->getAmount());
        $this->assertSame($this->tx->getAmount(), 999.99);

        $this->tx->setAmount('999.99');
        $this->assertNotNull($this->tx->getAmount());
        $this->assertSame($this->tx->getAmount(), '999.99');

    }

    public function testGetDate()
    {
        $this->assertNotNull($this->tx);
        $this->assertNull($this->tx->getDate());
    }

    /**
     * @depends testGetDate
     */
    public function testSetDate()
    {
        $this->tx->setDate('1419123412');
        $this->assertNotNull($this->tx->getDate());
        $this->assertSame($this->tx->getDate(), '1419123412');
    }
}
