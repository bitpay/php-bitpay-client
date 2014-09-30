<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay;

class AutoloaderTest extends \PHPUnit_Framework_TestCase
{

    protected function teardown()
    {
        Autoloader::unregister();
    }

    /**
     * Make sure that our autoloader is first in the queue
     */
    public function testRegister()
    {
        Autoloader::register();
        $functions = spl_autoload_functions();
        $this->assertSame(array('Bitpay\Autoloader','autoload'), $functions[0]);
    }

    public function testUnregister()
    {
        Autoloader::register();
        $numOfAutoloaders = count(spl_autoload_functions());
        Autoloader::unregister();
        $this->assertCount($numOfAutoloaders - 1, spl_autoload_functions());
    }

    public function testAutoload()
    {
        Autoloader::register();

        Autoloader::autoload('Bitpay\Bitpay');
        // Is only required once
        Autoloader::autoload('Bitpay\Bitpay');
    }

    /**
     */
    public function testNoClass()
    {
        Autoloader::autoload('Foo\Bar');
    }

    /**
     * @expectedException Exception
     */
    public function testException()
    {
        Autoloader::autoload('Bitpay\ClassThatWillNeverBeCreated');
    }

    public function testNoExceptionForBitpayClasslike()
    {
        Autoloader::register();

        // Magento Classes
        Autoloader::autoload('Bitpay_Core_Model');
    }
}
