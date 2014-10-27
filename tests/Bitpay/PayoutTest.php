<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay;

class PayoutTest extends \PHPUnit_Framework_TestCase
{
    private $payout;

    public function setUp()
    {
        $this->payout = new Payout();
    }

    public function testGetAmount()
    {
        $this->assertNotNull($this->payout);
        $this->assertNull($this->payout->getAmount());
    }

    /**
     * @depends testGetAmount
     */
    public function testGetAmountWithInstructions()
    {
        $this->assertNotNull($this->payout);
        $this->assertNull($this->payout->getAmount());

        $ins1 = new PayoutInstruction();
        $ins1->setAmount(10);

        $ins2 = new PayoutInstruction();
        $ins2->setAmount(25);

        $this->payout->addInstruction($ins1);
        $this->assertSame(10, $this->payout->getAmount());

        $this->payout->addInstruction($ins2);
        $this->assertSame(35, $this->payout->getAmount());
    }

    public function testGetInstructions()
    {
        $this->assertEmpty($this->payout->getInstructions());

        $i = new PayoutInstruction();
        $i  ->setAddress('bitcoin address')
            ->setAmount(101.99)
            ->setLabel('employee identifier');
        $this->payout->addInstruction($i);

        $l = $this->payout->getInstructions();
        $this->assertSame($l[0], $i);
        $this->assertSame('bitcoin address', $l[0]->getAddress());
        $this->assertSame(101.99, $l[0]->getAmount());
        $this->assertSame('employee identifier', $l[0]->getLabel());
        $this->assertTrue(count($this->payout->getInstructions()) == 1);
    }

    public function testGetId()
    {
        $this->assertNotNull($this->payout);
        $this->assertNull($this->payout->getId());
    }

    /**
     * @depends testGetId
     */
    public function testSetId()
    {
        $this->payout->setId('a4kFiay3KOueG');
        $this->assertNotNull($this->payout->getId());
        $this->assertSame('a4kFiay3KOueG', $this->payout->getId());
    }

    public function testGetAccountId()
    {
        $this->assertNotNull($this->payout);
        $this->assertNull($this->payout->getAccountId());
    }

    /**
     * @depends testGetId
     */
    public function testSetAccountId()
    {
        $this->payout->setAccountId('a4kFiay3KOueG');
        $this->assertNotNull($this->payout->getAccountId());
        $this->assertSame('a4kFiay3KOueG', $this->payout->getAccountId());
    }

    public function testAddInstruction()
    {
        $this->assertTrue(count($this->payout->getInstructions()) == 0);

        $instruction = new PayoutInstruction();
        $instruction
            ->setAddress('address test')
            ->setLabel('payout label')
            ->setAmount(10);

        $this->payout->addInstruction($instruction);
        $this->assertTrue(count($this->payout->getInstructions()) == 1);
        $instructions = $this->payout->getInstructions();
        $this->assertSame($instruction, $instructions[0]);

        $other = new PayoutInstruction();
        $other
            ->setAddress('other address')
            ->setLabel('id for employee')
            ->setAmount(50);

        $this->payout->addInstruction($other);
        $this->assertTrue(count($this->payout->getInstructions()) == 2);
        $instructions = $this->payout->getInstructions();
        $this->assertSame($other, $instructions[1]);
    }

    public function testUpdateInstruction()
    {
        $instruction = new PayoutInstruction();
        $instruction
            ->setAddress('address test')
            ->setLabel('payout label')
            ->setAmount(10);

        $this->payout->addInstruction($instruction);
        $this->payout->updateInstruction(0, 'setAddress', 'new address');
        $out = $this->payout->getInstructions();
        $this->assertNotSame($out[0]->getAddress(), 'address test');
        $this->assertSame($out[0]->getAddress(), 'new address');
    }

    public function testGetPricingMethod()
    {
        $this->assertNotNull($this->payout);
        $this->assertNull($this->payout->getPricingMethod());
    }

    /**
     * @depends testGetPricingMethod
     */
    public function testSetPricingMethod()
    {
        $this->payout->setPricingMethod('pricing method for payout');
        $this->assertNotNull($this->payout->getPricingMethod());
        $this->assertSame('pricing method for payout', $this->payout->getPricingMethod());
    }

    public function testGetNotificationEmail()
    {
        $this->assertNotNull($this->payout);
        $this->assertNull($this->payout->getNotificationEmail());
    }

    /**
     * @depends testGetNotificationEmail
     */
    public function testSetNotificationEmail()
    {
        $this->payout->setNotificationEmail('testemail@example.com');
        $this->assertNotNull($this->payout->getNotificationEmail());
        $this->assertSame('testemail@example.com', $this->payout->getNotificationEmail());
    }

    public function testGetNotificationUrl()
    {
        $this->assertNotNull($this->payout);
        $this->assertNull($this->payout->getNotificationUrl());
    }

    /**
     * @depends testGetNotificationEmail
     */
    public function testSetNotificationUrl()
    {
        $this->payout->setNotificationUrl('https://somesite.com/url.php');
        $this->assertNotNull($this->payout->getNotificationUrl());
        $this->assertSame('https://somesite.com/url.php', $this->payout->getNotificationUrl());
    }

    public function testGetCurrency()
    {
        $this->assertNotNull($this->payout);
        $this->assertNull($this->payout->getCurrency());
    }

    /**
     * @depends testGetCurrency
     */
    public function testSetCurrency()
    {
        $currency = new Currency();
        $currency
            ->setCode('USD')
            ->getSymbol('$');

        $this->payout->setCurrency($currency);
        $this->assertNotNull($this->payout->getCurrency());
        $this->assertSame($currency, $this->payout->getCurrency());
    }

    public function testGetEffectiveDate()
    {
        $this->assertNotNull($this->payout);
        $this->assertNull($this->payout->getEffectiveDate());
    }

    /**
     * @depends testGetEffectiveDate
     */
    public function testSetEffectiveDate()
    {
        $this->payout->setEffectiveDate('199999999');
        $this->assertNotNull($this->payout->getEffectiveDate());
        $this->assertSame('199999999', $this->payout->getEffectiveDate());

    }

    public function testGetRequestDate()
    {
        $this->assertNotNull($this->payout);
        $this->assertNull($this->payout->getRequestDate());
    }

    /**
     * @depends testGetRequestDate
     */
    public function testSetRequestDate()
    {
        $this->payout->setRequestDate('199999999');
        $this->assertNotNull($this->payout->getRequestDate());
        $this->assertSame('199999999', $this->payout->getRequestDate());
    }

    public function testGetRate()
    {
        $this->assertNotNull($this->payout);
        $this->assertNull($this->payout->getRate());
    }

    /**
     * @depends testGetRate
     */
    public function testSetRate()
    {
        $this->payout->setRate('1253.61');
        $this->assertNotNull($this->payout->getRate());
        $this->assertSame('1253.61', $this->payout->getRate());
    }

    public function testGetToken()
    {
        $this->assertNotNull($this->payout);
        $this->assertNull($this->payout->getToken());
    }

    /**
     * @depends testGetToken
     */
    public function testSetToken()
    {
        $token = new Token();
        $token->setFacade('payroll')
            ->setToken('40aufgbkadfkjgakjhg');

        $this->payout->setToken($token);
        $this->assertSame($this->payout->getToken(), $token);
    }

    public function testGetResponseToken()
    {
        $this->assertNotNull($this->payout);
        $this->assertNull($this->payout->getResponseToken());
    }

    /**
     * @depends testGetResponseToken
     */
    public function testSetResponseToken()
    {
        $this->payout->setResponseToken('4kjfgalksdfau3fiadwigdhfhladhrfwui3urfsdfg');
        $this->assertNotNull($this->payout->getResponseToken());
        $this->assertSame('4kjfgalksdfau3fiadwigdhfhladhrfwui3urfsdfg', $this->payout->getResponseToken());
    }

    public function testGetReference()
    {
        $this->assertNotNull($this->payout);
        $this->assertNull($this->payout->getReference());
    }

    /**
     * @depends testGetReference
     */
    public function testSetReference()
    {
        $this->payout->setReference('your reference for the payout');
        $this->assertNotNull($this->payout->getReference());
        $this->assertSame('your reference for the payout', $this->payout->getReference());
    }
}
