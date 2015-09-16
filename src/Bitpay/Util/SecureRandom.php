<?php
/**
 * @license Copyright 2011-2015 BitPay Inc., MIT License
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay\Util;

/**
 * Generates cryptographically-secure random numbers using the OpenSSL extension.
 *
 * @package Bitpay
 */
class SecureRandom
{
    /**
     * @var boolean
     */
    protected static $hasOpenSSL;

    /**
     * Generates 32 bytes of random data using the OpenSSL extension.
     *
     * @see http://php.net/manual/en/function.openssl-random-pseudo-bytes.php
     * @param integer $bytes
     * @return string $random
     * @throws \Exception
     */
    public static function generateRandom($bytes = 32)
    {
        if (!self::hasOpenSSL()) {
            throw new \Exception('You MUST have openssl module installed.');
        }

        $random = openssl_random_pseudo_bytes($bytes, $isStrong);

        if (!$random || !$isStrong) {
            throw new \Exception('Cound not generate a cryptographically strong random number.');
        }

        return $random;
    }

    /**
     * Returns true/false if the OpenSSL extension is installed & enabled.
     *
     * @return boolean
     */
    public static function hasOpenSSL()
    {
        if (null === self::$hasOpenSSL) {
            self::$hasOpenSSL = function_exists('openssl_random_pseudo_bytes');
        }

        return self::$hasOpenSSL;
    }
}
