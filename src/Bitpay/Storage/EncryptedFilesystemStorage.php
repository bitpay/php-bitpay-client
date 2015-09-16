<?php
/**
 * @license Copyright 2011-2015 BitPay Inc., MIT License
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay\Storage;

/**
 * @package Bitpay
 */
class EncryptedFilesystemStorage implements StorageInterface
{
    /**
     * @var string
     */
    private $password;

    /**
     * Initialization Vector
     */
    const IV = '0000000000000000';

    /**
     * @var string
     */
    const METHOD = 'AES-128-CBC';

    /**
     * @var int
     */
    const OPENSSL_RAW_DATA = 1;

    /**
     * @param string $password
     */
    public function __construct($password)
    {
        $this->password = $password;
    }

    /**
     * @inheritdoc
     */
    public function persist(\Bitpay\KeyInterface $key)
    {
        $path    = $key->getId();
        $data    = serialize($key);

        $encoded = base64_encode(openssl_encrypt(
            $data,
            self::METHOD,
            $this->password,
            1,
            self::IV
        ));

        if (file_put_contents($path, $encoded) === false) {
            throw new \Exception('[ERROR] In EncryptedFilesystemStorage::persist(): Could not write to the file "' . $path . '".');
        }
    }

    /**
     * @inheritdoc
     */
    public function load($id)
    {
        if (is_file($id) === false) {
            throw new \Exception('[ERROR] In EncryptedFilesystemStorage::load(): Could not find the file "' . $id . '".');
        }

        if (is_readable($id) === false) {
            throw new \Exception('[ERROR] In EncryptedFilesystemStorage::load(): The file "' . $id . '" cannot be read, check permissions.');
        }

        $encoded = file_get_contents($id);

        if ($encoded === false) {
            throw new \Exception('[ERROR] In EncryptedFilesystemStorage::load(): The file "' . $id . '" cannot be read, check permissions.');
        }

        $decoded = openssl_decrypt(base64_decode($encoded), self::METHOD, $this->password, 1, self::IV);

        if ($decoded === false) {
            throw new \Exception('[ERROR] In EncryptedFilesystemStorage::load(): Could not decode key "' . $id . '".');
        }

        return unserialize($decoded);
    }
}
