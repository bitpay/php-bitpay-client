<?php
/**
 * @license Copyright 2011-2015 BitPay Inc., MIT License
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay;

class KeyManagerTest extends \PHPUnit_Framework_TestCase
{
    private $temp_path;

    public function setUp()
    {
        $this->temp_path = '/tmp/mock.key';
    }

    public function testConstruct()
    {
        $storage = $this->getMockStorage();
        $this->assertNotNull($storage);

        $manager = new KeyManager($storage);
        $this->assertNotNull($manager);
    }

    /**
     * @depends testConstruct
     */
    public function testPersist()
    {
        $storage = $this->getMockStorage();
        $this->assertNotNull($storage);

        $manager = new KeyManager($storage);
        $this->assertNotNull($manager);

        $manager->persist($this->getMockKey());
    }

    /**
     * @depends testConstruct
     */
    public function testLoad()
    {
        $storage = $this->getMockStorage();
        $this->assertNotNull($storage);

        $manager = new KeyManager($storage);
        $this->assertNotNull($manager);
        $manager->load($this->getMockKey());
    }

    private function getMockKey()
    {
        return new \Bitpay\PublicKey($this->temp_path);
    }

    private function getMockStorage()
    {
        return new \Bitpay\Storage\MockStorage();
    }
}
