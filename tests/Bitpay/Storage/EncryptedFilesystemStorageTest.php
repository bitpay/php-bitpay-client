<?php
/**
 * The MIT License (MIT)
 *
 * Copyright (c) 2014 BitPay, Inc.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
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
