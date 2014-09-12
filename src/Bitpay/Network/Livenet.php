<?php

namespace Bitpay\Network;

/**
 *
 * @package Bitcore
 */
class Livenet implements NetworkInterface
{

    public function getName()
    {
        return 'livenet';
    }

    public function getAddressVersion()
    {
        return 0x00;
    }

    public function getApiHost()
    {
        return 'bitpay.com';
    }
}
