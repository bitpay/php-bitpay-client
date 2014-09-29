<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License 
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay;

class TokenTest extends \PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $token = new Token();
        $this->assertInstanceOf('Bitpay\TokenInterface', $token);
    }

    public function testToString()
    {
        $token = new Token();
        $this->assertSame('', (string) $token);
    }

    public function testGetToken()
    {
        $token = new Token();
        $this->assertNull($token->getToken());
    }

    /**
     * @depends testGetToken
     */
    public function testSetToken()
    {
        $token = new Token();
        $token->setToken('test');
        $this->assertSame('test', $token->getToken());
    }

    public function testGetResource()
    {
        $token = new Token();
        $this->assertNull($token->getResource());
    }

    /**
     * @depends testGetResource
     */
    public function testSetResource()
    {
        $token = new Token();
        $token->setResource('test');
        $this->assertSame('test', $token->getResource());
    }

    public function testGetFacade()
    {
        $token = new Token();
        $this->assertNull($token->getFacade());
    }

    /**
     * @depends testGetFacade
     */
    public function testSetFacade()
    {
        $token = new Token();
        $token->setFacade('pos');
        $this->assertSame('pos', $token->getFacade());
    }

    public function testGetCreatedAt()
    {
        $token = new Token();
        $this->assertNull($token->getCreatedAt());
    }

    /**
     * @depends testGetCreatedAt
     */
    public function testSetCreatedAt()
    {
        $token = new Token();
        $token->setCreatedAt('createdAt');
        $this->assertSame('createdAt', $token->getCreatedAt());
    }

    public function testGetPolicies()
    {
        $token = new Token();
        $this->assertCount(0, $token->getPolicies());
    }

    /**
     * @depends testGetPolicies
     */
    public function testSetPolicies()
    {
        $token = new Token();
        $token->setPolicies('policy');
        $this->assertSame('policy', $token->getPolicies());
    }
}
