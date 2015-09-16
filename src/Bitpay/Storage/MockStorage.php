<?php
/**
 * @license Copyright 2011-2015 BitPay Inc., MIT License
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay\Storage;

/**
 * @codeCoverageIgnore
 * @package Bitpay
 */
class MockStorage implements StorageInterface
{
    /**
     * @param Key $key
     */
    public function persist(\Bitpay\KeyInterface $key)
    {
    }

    /**
     * @param string $id
     * @return void
     */
    public function load($id)
    {
        return;
    }
}
