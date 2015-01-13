<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay;

class PayoutInstructionTest extends \PHPUnit_Framework_TestCase
{
    private $instruction;

    public function setUp()
    {
        $this->instruction = new PayoutInstruction();
    }

    public function testGetId()
    {
        $this->assertNotNull($this->instruction);
        $this->assertNull($this->instruction->getId());
    }

    public function testGetBtc()
    {
        $this->assertNotNull($this->instruction);
        $this->assertNull($this->instruction->getBtc());
    }

    public function testGetLabel()
    {
        $this->assertNotNull($this->instruction);
        $this->assertNull($this->instruction->getLabel());
    }

    public function testGetAddress()
    {
        $this->assertNotNull($this->instruction);
        $this->assertNull($this->instruction->getAddress());
    }

    public function testGetAmount()
    {
        $this->assertNotNull($this->instruction);
        $this->assertNull($this->instruction->getAmount());
    }

    public function testGetStatus()
    {
        $this->assertNotNull($this->instruction);
        $this->assertNull($this->instruction->getStatus());
    }

    public function testGetTransactions()
    {
        $this->assertEmpty($this->instruction->getTransactions());
        $this->assertInternalType('array', $this->instruction->getTransactions());
    }

    public function testAddTransaction()
    {
        $this->assertTrue(count($this->instruction->getTransactions()) == 0);

        $date = new \DateTime();
        $tx = new PayoutTransaction();
        $tx
            ->setTransactionId('00000000b456d28e2769762d1de5c8c668282c5dca566c226c455da4bd20aa78')
            ->setAmount(10)
            ->setDate($date);

        $this->instruction->addTransaction($tx);
        $this->assertTrue(count($this->instruction->getTransactions()) == 1);
        $o = $this->instruction->getTransactions();
        $this->assertSame($o[0], $tx);

        $tx2 = new PayoutTransaction();
        $tx2
            ->setTransactionId('41414141b456d28e2769762d1de5c8c668282c5dca566c226c455da4bd20aa78')
            ->setAmount(10.50)
            ->setDate($date);

        $this->instruction->addTransaction($tx2);
        $this->assertTrue(count($this->instruction->getTransactions()) == 2);
        $o = $this->instruction->getTransactions();
        $this->assertSame($o[1], $tx2);
    }

    /**
     * @depends testGetId
     */
    public function testSetId()
    {
        $this->instruction->setId('4Ka14LaMfba');
        $this->assertNotNull($this->instruction->getId());
        $this->assertSame('4Ka14LaMfba', $this->instruction->getId());
    }

    /**
     * @depends testGetLabel
     */
    public function testSetLabel()
    {
        $this->instruction->setLabel('employee label');
        $this->assertNotNull($this->instruction->getLabel());
        $this->assertSame('employee label', $this->instruction->getLabel());
    }

    /**
     * @depends testGetAddress
     */
    public function testSetAddress()
    {
        $this->instruction->setAddress('1DaXKDhevzucn1W9ZsBeDkPUAEd2y9QKJb');
        $this->assertNotNull($this->instruction->getAddress());
        $this->assertSame('1DaXKDhevzucn1W9ZsBeDkPUAEd2y9QKJb', $this->instruction->getAddress());
    }

    /**
     * @depends testGetBtc
     */
    public function testSetBtc()
    {
        $btc = array('unpaid' => null, 'paid' => '0');
        $this->instruction->setBtc($btc);
        $this->assertNotNull($this->instruction->getBtc());
        $this->assertSame($btc, $this->instruction->getBtc());
    }
    /**
     * @depends testGetAmount
     */
    public function testSetAmount()
    {
        $this->instruction->setAmount('10.99');
        $this->assertNotNull($this->instruction->getAmount());
        $this->assertInternalType('string', $this->instruction->getAmount());
        $this->assertSame('10.99', $this->instruction->getAmount());

        $this->instruction->setAmount(10.99);
        $this->assertNotNull($this->instruction->getAmount());
        $this->assertInternalType('float', $this->instruction->getAmount());
        $this->assertSame(10.99, $this->instruction->getAmount());
    }
    /**
     * @depends testGetStatus
     */
    public function testSetStatus()
    {
        $this->instruction->setStatus('processing');
        $this->assertNotNull($this->instruction->getStatus());
        $this->assertSame('processing', $this->instruction->getStatus());
    }
}
