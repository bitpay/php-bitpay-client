<?php

namespace Bitpay;

class AccessTokenTest extends \PHPUnit_Framework_TestCase
{

    public function testId()
    {
        $token = new AccessToken();
        $token->setId('test');

        $this->assertSame('test', $token->getId());
    }

    public function testEmail()
    {
        $token = new AccessToken();
        $token->setEmail('test@test.com');

        $this->assertSame('test@test.com', $token->getEmail());
    }

    public function testLabel()
    {
        $token = new AccessToken();
        $token->setLabel('label');

        $this->assertSame('label', $token->getLabel());
    }

    public function testNonce()
    {
        $token = new AccessToken();
        $this->assertFalse($token->isNonceDisabled());
    }

    public function testNonceDisable()
    {
        $token = new AccessToken();
        $this->assertFalse($token->isNonceDisabled());
        $token->nonceDisable();
        $this->assertTrue($token->isNonceDisabled());
    }

    public function testNonceEnable()
    {
        $token = new AccessToken();
        $this->assertFalse($token->isNonceDisabled());
        $token->nonceEnable();
        $this->assertFalse($token->isNonceDisabled());
    }
}
