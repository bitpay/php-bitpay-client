<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay\Storage;

use org\bovigo\vfs\vfsStream;

class FilesystemStorageTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->root = vfsStream::setup('tmp');
    }

    public function testPersist()
    {
        $storage = new FilesystemStorage();
        $storage->persist(new \Bitpay\PublicKey(vfsStream::url('tmp/public.key')));
        $this->assertTrue($this->root->hasChild('tmp/public.key'));
    }

    public function testLoad()
    {
        $storage = new FilesystemStorage();

        vfsStream::newFile('public.key')
            ->at($this->root)
            ->setContent('C:16:"Bitpay\PublicKey":62:{a:5:{i:0;s:20:"vfs://tmp/public.key";i:1;N;i:2;N;i:3;N;i:4;N;}}');

        $key = $storage->load(vfsStream::url('tmp/public.key'));
        $this->assertInstanceOf('Bitpay\PublicKey', $key);
    }

    /**
     * @expectedException Exception
     */
    public function testNotFileException()
    {
        $storage = new FilesystemStorage();
        $storage->load(vfsStream::url('tmp/public.key'));
    }

    /**
     * @expectedException Exception
     */
    public function testLoadNotReadableException()
    {
        $storage = new FilesystemStorage();
        vfsStream::newFile('public.key', 0600)
            ->at($this->root)
            ->setContent('C:16:"Bitpay\PublicKey":62:{a:5:{i:0;s:20:"vfs://tmp/public.key";i:1;N;i:2;N;i:3;N;i:4;N;}}')
            ->chown(vfsStream::OWNER_ROOT)
            ->chgrp(vfsStream::GROUP_ROOT);
        $storage->load(vfsStream::url('tmp/public.key'));
    }
}
