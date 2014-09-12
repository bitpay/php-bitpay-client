<?php

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
