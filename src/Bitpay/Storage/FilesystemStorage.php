<?php
/**
 * @license Copyright 2011-2015 BitPay Inc., MIT License
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay\Storage;

/**
 * Used to persist keys to the filesystem.
 *
 * @package Bitpay
 */
class FilesystemStorage implements StorageInterface
{
    /**
     * @inheritdoc
     */
    public function persist(\Bitpay\KeyInterface $key)
    {
        try {
            $path = $key->getId();
            file_put_contents($path, serialize($key));
        } catch (\Exception $e) {
            throw new \Exception('[ERROR] In FilesystemStorage::persist(): ' . $e->getMessage());
        }
    }

    /**
     * @inheritdoc
     */
    public function load($id)
    {
        if (!is_file($id)) {
            throw new \Exception(sprintf('[ERROR] In FilesystemStorage::load(): Could not find "%s".', $id));
        }

        if (!is_readable($id)) {
            throw new \Exception(sprintf('[ERROR] In FilesystemStorage::load(): "%s" cannot be read, check permissions.', $id));
        }

        return unserialize(file_get_contents($id));
    }
}
