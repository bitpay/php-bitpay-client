<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay\Storage;

use org\bovigo\vfs\vfsStream;

class EncryptedFilesystemStorageTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->root = vfsStream::setup('tmp');
    }

    public function testPersist()
    {
        $storage = new EncryptedFilesystemStorage('dN$8WNaT}j<gD3*q');
        $storage->persist(new \Bitpay\PublicKey(vfsStream::url('tmp/public.key')));
        $this->assertTrue($this->root->hasChild('tmp/public.key'));
    }

    public function testUnencodedLoad()
    {
        $storage = new EncryptedFilesystemStorage('dN$8WNaT}j<gD3*q');

        vfsStream::newFile('public.key')
            ->at($this->root)
            ->setContent('97f394f4153a75ad7da3d9b8844cf0593c5abbea78695ba41c8528bc7bcd158cdc73a93ba2826d4a9ef1b3552f0f754a2db43010488ebea5648c7d897749df8d27d761683de7c17d225d2464413c89cc5ddc6d0a1b522b0efbeb47cc247a9ecb8d4e5a8790a2eb82cf1a9b6a727b90e3de444f2245b3c6aae7a1f12f3727ad926935f6540a49fd7e7e633b613ee1a196ed56b8c19d2c9353a4d9ee1f94f6a39f0a5bcf6729ea9677e9d9d590e53cdf25e04e2b00ee31f2489b7ae42cb0666dd002b536a95224f11ca0a9dc771b3eaf230b2f2bad72e13837308a58e0acfc03d2ac53522dad3231e754c647c75282bc9882f4b9d4ab712cb901f6d4d03c346df444c4e2a2a2114fd22a3c396c2a0e8ddf6838fa3fc54ea72b5095807c3a6f402cfd7ad15da7b45630bb31ed4c7e95bdfdff477c6b9c0e48fe678266d6b15505eeb2a0e8ff60b400af3f376fd261619fa9c1233efbb75ace29f9dfbc6360f9e4ac7d53e1bf112fa2ab8740f53dd40318da0e7360cb40cbcc15fe3c589bf34fdf7b981800b50d666b2e795438ce22b2640b4d55b98fa08aa37e18d6581e198d5c960574b07ab7daaf89b9e361719d85040a0c1e53b51f96f3119b27f922ab1ae7989bc8d686860f8c2d7201fe427a401be9dba0fd19ced8124d99b1475f75f007bf8cf213065c52544b3dffc126b05c5c3b2965ffacd4a16a395f11341a0149023b9b6df3326a6161cdb28d2e71690560a2');

        $key = $storage->load(vfsStream::url('tmp/public.key'));
        $this->assertInstanceOf('Bitpay\PublicKey', $key);
    }

    public function testLoad()
    {
        $storage = new EncryptedFilesystemStorage('dN$8WNaT}j<gD3*q');

        vfsStream::newFile('public.key')
            ->at($this->root)
            ->setContent('222464cf5e76807259205ed98a1114e3164ecada597352259f9e09ce06524cdf7eaf862bff208d44163b8b24719afbfe031344d97a44502955b63158e012c09f604f66f3c7ca99290e9991a4a1413c3a4095fb6522653ceda25d831d115be3de6756fd7511ed91d970dac0e4e01a5df91ce9b412a0c8a42eb266cf2a93e1d1b50090b3eb89c93fcd85ed2cee6ee08499a6d69c3ffe7836878e30a37df92226fc78a2936f6037d8bf4b0a33bb11dad77b544a1baaf1c097be38d04f6b642285811b9c9e27c51d460a57e298851bb047f6fa2a02f501e7902a660fa66630240cf8586f6cf774b6b0a6e62a06de6eee328b3dfaa3658fd692b0e7590ea58281c8a563e3fe09a1209de96c7919a3b92c5307b782b4729a68b08f220b03df02c15e7742977a48b48c4bff7d060020e3b4717d5ae05d630e9e5f1374d2bb9ac04652ee12ef9de37c67cd07461ae7a201ddd04975ec4f60e781e214b50ca1b756988b7a1868fdbf2b07db66e0e7c9a29526c7d11127bb58c606c515325d7375a21c9d1db63167fb34106c87c49d238fc1eb41309eb5f23d946743534bf8d454729dd94ac9c3d18cb7261fd773c913e674a4427c0b90b4f9a541b77363d43f957445fff395ad5c48b51131913917882413ce8084db0d20ab308b3504d43e8f67afb4f6611324d07d8980ffc8d9ae6817bfe1be5e4f5ad6e5155b9767d15c01c96fc101538071f9bef40978f805e52e31a8e169bf');

        $key = $storage->load(vfsStream::url('tmp/public.key'));
        $this->assertInstanceOf('Bitpay\PublicKey', $key);

    }

    /**
     * @expectedException Exception
     */
    public function testNotFileException()
    {
        $storage = new EncryptedFilesystemStorage('satoshi');
        $storage->load(vfsStream::url('tmp/public.key'));
    }

    /**
     * @expectedException Exception
     */
    public function testLoadNotReadableException()
    {
        $storage = new EncryptedFilesystemStorage('satoshi');
        vfsStream::newFile('public.key', 0600)
            ->at($this->root)
            ->setContent('')
            ->chown(vfsStream::OWNER_ROOT)
            ->chgrp(vfsStream::GROUP_ROOT);
        $storage->load(vfsStream::url('tmp/public.key'));
    }

    /**
     * @expectedException Exception
     */
    public function testLoadCouldNotDecode()
    {
        $storage = new EncryptedFilesystemStorage('satoshi');

        vfsStream::newFile('public.key')
            ->at($this->root)
            ->setContent('00');

        $key = $storage->load(vfsStream::url('tmp/public.key'));
        $this->assertInstanceOf('Bitpay\PublicKey', $key);
    }

    public function testPersistAndLoadWithoutPassword()
    {
        $storage = new EncryptedFilesystemStorage(null);
        $storage->persist(new \Bitpay\PublicKey(vfsStream::url('tmp/public.key')));
        $this->assertTrue($this->root->hasChild('tmp/public.key'));

        $key = $storage->load(vfsStream::url('tmp/public.key'));
        $this->assertInstanceOf('Bitpay\PublicKey', $key);
    }

}
