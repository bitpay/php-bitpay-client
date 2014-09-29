<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay;

class AutoloaderTest extends \PHPUnit_Framework_TestCase
{
    public function testAutoload()
    {
        Autoloader::register();

        Autoloader::autoload('Bitpay/Bitpay');
        // Is only required once
        Autoloader::autoload('Bitpay/Bitpay');
    }

    /**
     */
    public function testNoClass()
    {
        Autoloader::autoload('Foo/Bar');
    }

    /**
     * @expectedException Exception
     */
    public function testException()
    {
        Autoloader::autoload('Bitpay/ClassThatWillNeverBeCreated');
    }
}
