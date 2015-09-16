<?php
/**
 * @license Copyright 2011-2015 BitPay Inc., MIT License
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay;

use org\bovigo\vfs\vfsStream;

class BitpayTest extends \PHPUnit_Framework_TestCase
{
    private $temp_path_pri;
    private $temp_path_pub;
    private $temp_path_root;
    private $network_type;

    public function setUp()
    {
        $this->temp_path_root = 'tmp';
        $this->temp_path_pri  = $this->temp_path_root . '/key.pri';
        $this->temp_path_pub  = $this->temp_path_root . '/key.pub';
        $this->network_type   = 'testnet';
    }

    public function testConstruct()
    {
        $bitpay = new \Bitpay\Bitpay(
            array(
                'bitpay' => array(
                    'network' => $this->network_type,
                )
            )
        );
    }

    public function testGetContainer()
    {
        $bitpay = new \Bitpay\Bitpay();
        $this->assertInstanceOf('Symfony\Component\DependencyInjection\Container', $bitpay->getContainer());
    }

    public function testGet()
    {
        $bitpay = new \Bitpay\Bitpay();
        $this->assertInstanceOf('Bitpay\Network\Livenet', $bitpay->get('network'));
    }

    /**
     * @expectedException Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     */
    public function testGetInvalidService()
    {
        $bitpay = new \Bitpay\Bitpay();
        $bitpay->get('coins');
    }

    public function testConfigAbleToPersistAndLoadKeys()
    {
        $root   = vfsStream::setup($this->temp_path_root);
        $bitpay = new \Bitpay\Bitpay(
            array(
                'bitpay' => array(
                    'network'     => 'testnet',
                    'private_key' => vfsStream::url($this->temp_path_pri),
                    'public_key'  => vfsStream::url($this->temp_path_pub),
                )
            )
        );

        $pri = new \Bitpay\PrivateKey(vfsStream::url($this->temp_path_pri));
        $pri->generate();

        $pub = new \Bitpay\PublicKey(vfsStream::url($this->temp_path_pub));
        $pub->setPrivateKey($pri)->generate();

        /**
         * Save keys to the filesystem
         */
        $storage = $bitpay->get('key_storage');
        $storage->persist($pri);
        $storage->persist($pub);

        /**
         * This will load the keys, if you have not already persisted them, than
         * this WILL throw an Exception since this will load the keys from the
         * storage class
         */
        $pri = $bitpay->get('private_key');
        $pub = $bitpay->get('public_key');
    }
}
