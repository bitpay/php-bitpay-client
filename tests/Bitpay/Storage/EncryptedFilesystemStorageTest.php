<?php
/**
 * @license Copyright 2011-2015 BitPay Inc., MIT License
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay\Storage;

use org\bovigo\vfs\vfsStream;

class EncryptedFilesystemStorageTest extends \PHPUnit_Framework_TestCase
{
    private $root;

    public function setUp()
    {
        $this->root = vfsStream::setup('tmp');
    }

    public function testPersist()
    {
        $storage = new EncryptedFilesystemStorage('satoshi');
        $storage->persist(new \Bitpay\PublicKey(vfsStream::url('tmp/public.key')));
        $this->assertTrue($this->root->hasChild('tmp/public.key'));
    }

    public function testLoad()
    {
        $storage = new EncryptedFilesystemStorage('satoshi');

        vfsStream::newFile('public.key')
            ->at($this->root)
            ->setContent('8bc03b8e4272d47ea81d63c6571b8172072ed03203ff7cd3fd434c03f7994b5721363d0dda3cec833f6f263bde0ececa06b79f68d5616be18b8e9311c486223e18c7424daaa59991f4b10db9f2fb8b4c42896c50d216010b403d562738ef5a96');

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
