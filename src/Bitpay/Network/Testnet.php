<?php

namespace Bitpay\Network;

/**
 *
 * @package Bitcore
 */
class Testnet implements NetworkInterface
{

    public function getName()
    {
        return 'testnet';
    }

    public function getAddressVersion()
    {
        return 0x6f;
    }

    public function getApiHost()
    {
        return 'test.bitpay.com';
    }
}
