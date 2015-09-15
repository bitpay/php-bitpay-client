<?php
/**
 * @license Copyright 2011-2015 BitPay Inc., MIT License
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay;

class AccessTokenTest extends \PHPUnit_Framework_TestCase
{
    public function testId()
    {
        $token = new AccessToken();

        $this->assertNotNull($token);
        $token->setId('test');
        $this->assertSame('test', $token->getId());
    }

    public function testEmail()
    {
        $token = new AccessToken();

        $this->assertNotNull($token);
        $token->setEmail('support@bitpay.com');
        $this->assertSame('support@bitpay.com', $token->getEmail());
    }

    public function testLabel()
    {
        $token = new AccessToken();

        $this->assertNotNull($token);
        $token->setLabel('label');
        $this->assertSame('label', $token->getLabel());
    }
}
