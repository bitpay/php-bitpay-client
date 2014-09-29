<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay\Network;

class LivenetTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->network = new Livenet();
    }

    public function testGetName()
    {
        $this->assertSame('livenet', $this->network->getName());
    }

    public function testGetAddressVersion()
    {
        $this->assertSame(0x00, $this->network->getAddressVersion());
    }

    public function testGetApiHost()
    {
        $this->assertSame('bitpay.com', $this->network->getApiHost());
    }
}
