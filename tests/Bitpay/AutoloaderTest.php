<?php

namespace Bitpay;

use Bitpay\Autoloader;

class AutoloaderTest extends \PHPUnit_Framework_TestCase
{

    public function testAutoload()
    {
        Autoloader::register();

        // Only BitPay classes
        Autoloader::autoload('FooBar/Bitpay');

        Autoloader::autoload('Bitpay/Bitpay');
        // Is only required once
        Autoloader::autoload('Bitpay/Bitpay');
    }

    /**
     * @expectedException Exception
     */
    public function testException()
    {
        Autoloader::autoload('Bitpay/ClassThatWillNeverBeCreated');
    }
}
