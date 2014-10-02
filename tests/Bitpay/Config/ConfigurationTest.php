<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay\Config;

use Symfony\Component\Config\Definition\Processor;

class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    public function testProcessAndValidate()
    {
        $processor       = new Processor();
        $processedConfig = $processor->processConfiguration(
            new Configuration(),
            array()
        );

        $this->assertArrayHasKey('public_key', $processedConfig);
        $this->assertArrayHasKey('private_key', $processedConfig);
        $this->assertArrayHasKey('sin_key', $processedConfig);
        $this->assertArrayHasKey('network', $processedConfig);
        $this->assertArrayHasKey('adapter', $processedConfig);
        $this->assertArrayHasKey('key_storage', $processedConfig);
        $this->assertArrayHasKey('key_storage_password', $processedConfig);
        $this->assertCount(7, $processedConfig);
    }

    /**
     * @expectedException Exception
     */
    public function testInvalidKeyStorageClass()
    {
        $processor       = new Processor();
        $processedConfig = $processor->processConfiguration(
            new Configuration(),
            array(
                'bitpay' => array(
                    'key_storage' => 'Foo\Bar'
                )
            )
        );
    }
}
