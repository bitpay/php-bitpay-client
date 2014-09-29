<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay\Network;

class TestnetTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->network = new Testnet();
    }

    public function testGetName()
    {
        $this->assertSame('testnet', $this->network->getName());
    }

    public function testGetAddressVersion()
    {
        $this->assertSame(0x6f, $this->network->getAddressVersion());
    }

    public function testGetApiHost()
    {
        $this->assertSame('test.bitpay.com', $this->network->getApiHost());
    }
}
