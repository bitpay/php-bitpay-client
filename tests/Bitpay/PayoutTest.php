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
        $id = 'a4kFiay3KOueG';
        $this->payout->setId($id);
        $this->assertNotNull($this->payout->getId());
        $this->assertSame($id, $this->payout->getId());
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
        $account = 'a4kFiay3KOueG';
        $this->payout->setAccountId($account);
        $this->assertNotNull($this->payout->getAccountId());
        $this->assertSame($account, $this->payout->getAccountId());
    }

    public function testGetAmount()
    {
        $this->assertNotNull($this->payout);
        $this->assertNull($this->payout->getAmount());
    }

    public function testGetInstructionsEmpty()
    {
        $this->assertEmpty($this->payout->getInstructions());
        $this->assertInternalType('array', $this->payout->getInstructions());
    }

    /**
     * @depends testGetInstructionsEmpty
     */
    public function testGetInstructions()
    {
        $label   = 'employee identifier';
        $amount  = 101.99;
        $address = '1AcAj9p6zJn4xLXdvmdiuPCtY7YkBPTAJo';

        $instruction = new PayoutInstruction();
        $instruction
            ->setAddress($address)
            ->setAmount($amount)
            ->setLabel($label);

        $this->payout->addInstruction($instruction);

        $this->assertInternalType('array', $this->payout->getInstructions());
        $this->assertTrue(count($this->payout->getInstructions()) == 1);

        $list = $this->payout->getInstructions();
        $this->assertSame($list[0], $instruction);

    }

    /**
     * @depends testGetInstructions
     */
    public function testAddInstruction()
    {
        $this->assertTrue(count($this->payout->getInstructions()) == 0);

        $instruction = new PayoutInstruction();
        $instruction
            ->setAddress('1LbEH8w3KGfTTt2L69bSR3d73njvvpm5tU')
            ->setLabel('payout label')
            ->setAmount(10);

        $this->payout->addInstruction($instruction);
        $this->assertTrue(count($this->payout->getInstructions()) == 1);

        $instructions = $this->payout->getInstructions();
        $this->assertSame($instruction, $instructions[0]);


        $other = new PayoutInstruction();
        $other
            ->setAddress('1L3cPDuruzGLfmmGbJgwoAjU51D6AqxaKK')
            ->setLabel('id for employee')
            ->setAmount(50);

        $this->payout->addInstruction($other);
        $this->assertTrue(count($this->payout->getInstructions()) == 2);

        $instructions = $this->payout->getInstructions();
        $this->assertSame($other, $instructions[1]);
    }

    /**
     * @depends testAddInstruction
     */
    public function testUpdateInstruction()
    {
        $address1 = '1NCNN8UKCZAjGkcgzA2RMZSHy9ao4YSWj7';
        $address2 = '1FXXRhvwtrGzBH65aK6kVwDeHq7hEXLuF9';

        $instruction = new PayoutInstruction();
        $instruction
            ->setAddress($address1)
            ->setAmount(10);

        $this->payout->addInstruction($instruction);
        $this->payout->updateInstruction(0, 'setAddress', $address2);

        $out = $this->payout->getInstructions();
        $this->assertNotSame($out[0]->getAddress(), $address1);
        $this->assertSame($out[0]->getAddress(), $address2);
    }

    public function testGetBtcAmountNull()
    {
        $this->assertNotNull($this->payout);
        $this->assertNull($this->payout->getBtcAmount());
    }

    /**
     * @depends testGetBtcAmountNull
     */
    public function testGetBtcAmount()
    {
        $amount = '4.3';
        $this->payout->setBtcAmount($amount);
        $this->assertSame($this->payout->getBtcAmount(), $amount);

        $amount = 4.3;
        $this->payout->setBtcAmount($amount);
        $this->assertSame($this->payout->getBtcAmount(), $amount);
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
        $method = 'bitcoinbestbuy';
        $this->payout->setPricingMethod($method);
        $this->assertNotNull($this->payout->getPricingMethod());
        $this->assertSame($method, $this->payout->getPricingMethod());
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
        $email = 'support@bitpay.com';
        $this->payout->setNotificationEmail($email);
        $this->assertNotNull($this->payout->getNotificationEmail());
        $this->assertSame($email, $this->payout->getNotificationEmail());
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
        $url = 'https://bitpay.com/';
        $this->payout->setNotificationUrl($url);
        $this->assertNotNull($this->payout->getNotificationUrl());
        $this->assertSame($url, $this->payout->getNotificationUrl());
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
        $currency = $this->getMockCurrency();
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
        $date = new \DateTime();
        $this->payout->setEffectiveDate($date);
        $this->assertNotNull($this->payout->getEffectiveDate());
        $this->assertSame($date, $this->payout->getEffectiveDate());

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
        $date = new \DateTime();
        $this->payout->setRequestDate($date);
        $this->assertNotNull($this->payout->getRequestDate());
        $this->assertSame($date, $this->payout->getRequestDate());
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
        $rate = '1253.61';
        $this->payout->setRate($rate);
        $this->assertNotNull($this->payout->getRate());
        $this->assertSame($rate, $this->payout->getRate());
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
        $token
            ->setFacade('payroll')
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

    private function getMockCurrency()
    {
        return new \Bitpay\Currency();
    }
}
