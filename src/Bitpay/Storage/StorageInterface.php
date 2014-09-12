<?php

namespace Bitpay\Storage;

/**
 * @package Bitcore
 */
interface StorageInterface
{

    /**
     * @param KeyInterface $key
     */
    public function persist(\Bitpay\KeyInterface $key);

    /**
     * @param string $id
     *
     * @return KeyInterface
     */
    public function load($id);
}
