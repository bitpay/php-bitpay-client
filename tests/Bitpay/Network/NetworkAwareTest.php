<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay\Network;

class NetworkAwareTest extends \PHPUnit_Framework_TestCase
{
    public function testSetNetwork()
    {
        $networkAware = $this->getMockForAbstractClass('Bitpay\Network\NetworkAware');
        $networkAware->setNetwork(new \Bitpay\Network\Testnet());

        $r = new \ReflectionObject($networkAware);
        $network = $r->getProperty('network');
        $network->setAccessible(true);
        $network = $network->getValue($networkAware);

        $this->assertInstanceOf('Bitpay\Network\NetworkInterface', $network);
    }
}
