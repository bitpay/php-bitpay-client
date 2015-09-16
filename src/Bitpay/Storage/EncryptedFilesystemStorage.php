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
        $msg  = '';
        $openssl_error_msg = '';
        $path = $key->getId();
        $data = serialize($key);

        $encrypted = openssl_encrypt($data, self::METHOD, $this->password, self::OPENSSL_RAW_DATA, self::IV);

        if ($encrypted === false) {
            while ($msg = openssl_error_string()) {
                $openssl_error_msg .= $msg . "\r\n";
                $msg = '';
            }
            
            throw new \Exception('[ERROR] In EncryptedFilesystemStorage::persist(): Could not encrypt data for key file "' . $id . '" with the data "' . $data . '". OpenSSL error(s) are: "' . $openssl_error_msg . '".');
        }

        $encoded = base64_encode($encrypted);

        if ($encoded === false) {
            throw new \Exception('[ERROR] In EncryptedFilesystemStorage::persist(): Could not encode encrypted data for key file "' . $id . '" with the data "' . $data . '".');
        }

        if (file_put_contents($path, $encoded) === false) {
            throw new \Exception('[ERROR] In EncryptedFilesystemStorage::persist(): Could not write to the file "' . $path . '".');
        }
    }

    /**
     * @inheritdoc
     */
    public function load($id)
    {
        $msg = '';
        $openssl_error_msg = '';

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

        $decoded = base64_decode($encoded);

        if ($decoded === false) {
            throw new \Exception('[ERROR] In EncryptedFilesystemStorage::load(): Could not decode encrypted data for key file "' . $id . '" with the data "' . $encoded . '".');
        }

        $decrypted = openssl_decrypt($decoded, self::METHOD, $this->password, self::OPENSSL_RAW_DATA, self::IV);

        if ($decrypted === false) {
            while ($msg = openssl_error_string()) {
                $openssl_error_msg .= $msg . "\r\n";
                $msg = '';
            }

            throw new \Exception('[ERROR] In EncryptedFilesystemStorage::load(): Could not decrypt key "' . $id . '" with data "' . $decoded . '". OpenSSL error(s) are: "' . $openssl_error_msg . '".');
        }

        return unserialize($decrypted);
    }
}
