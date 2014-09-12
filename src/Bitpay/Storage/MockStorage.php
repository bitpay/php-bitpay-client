<?php

namespace Bitpay\Storage;

/**
 * @codeCoverageIgnore
 * @package Bitcore
 */
class MockStorage implements StorageInterface
{

    public function persist(\Bitpay\KeyInterface $key)
    {
    }

    public function load($id)
    {
        return;
    }
}
