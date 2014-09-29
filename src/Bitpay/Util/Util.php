<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License 
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay\Util;

/**
 * Utility class used by string and arbitrary integer methods.
 *
 * @package Bitcore
 */
class Util
{
    /**
     * @var string
     */
    const HEX_CHARS = '0123456789abcdef';

    /**
     * Computes a digest hash value for the given data using
     * the given method, and returns a raw or binhex encoded
     * string, see:
     * http://us1.php.net/manual/en/function.openssl-digest.php
     *
     * @param string $data
     *
     * @return string
     */
    public static function sha256($data, $binary = false)
    {
        return openssl_digest($data, 'SHA256', $binary);
    }

    /**
     * Computes a digest hash value for the given data using
     * the given method, and returns a raw or binhex encoded
     * string, see:
     * http://us1.php.net/manual/en/function.openssl-digest.php
     *
     * @param string $data
     *
     * @return string
     */
    public static function sha512($data)
    {
        return openssl_digest($data, 'sha512');
    }

    /**
     * Generate a keyed hash value using the HMAC method.
     * http://us1.php.net/manual/en/function.hash-hmac.php
     *
     * @param string $data
     * @param string $key
     *
     * @return string
     */
    public static function sha512hmac($data, $key)
    {
        return hash_hmac('SHA512', $data, $key);
    }

    /**
     * Returns a RIPDEMD160 hash of a value.
     *
     * @param string $data
     *
     * @return string
     */
    public static function ripe160($data, $binary = false)
    {
        return openssl_digest($data, 'ripemd160', $binary);
    }

    /**
     * Returns a SHA256 hash of a RIPEMD160 hash of a value.
     *
     * @param string $data
     *
     * @return string
     */
    public static function sha256ripe160($data)
    {
        return bin2hex(self::ripe160(self::sha256($data, true), true));
    }

    /**
     * Returns a double SHA256 hash of a value.
     *
     * @param string $data
     *
     * @return string
     */
    public static function twoSha256($data, $binary = false)
    {
        //$pass1 = self::sha256(hex2bin($data), true);
        //$pass2 = strrev(bin2hex(self::sha256($pass1, true)));

        return self::sha256(self::sha256($data, $binary), $binary);
    }

    /**
     * Returns a nonce for use in REST calls.
     *
     * @see http://en.wikipedia.org/wiki/Cryptographic_nonce
     *
     * @return string
     */
    public static function nonce()
    {
        return microtime(true);
    }

    /**
     * Returns a GUID for use in REST calls.
     *
     * @see http://en.wikipedia.org/wiki/Globally_unique_identifier
     *
     * @return string
     */
    public static function guid()
    {
        return sprintf(
            '%s-%s-%s-%s-%s',
            bin2hex(openssl_random_pseudo_bytes(4)),
            bin2hex(openssl_random_pseudo_bytes(2)),
            bin2hex(openssl_random_pseudo_bytes(2)),
            bin2hex(openssl_random_pseudo_bytes(2)),
            bin2hex(openssl_random_pseudo_bytes(6))
        );
    }

    /**
     * Encodes a decimal value into hexadecimal.
     *
     * @param string $dec
     * @return string
     */
    public static function encodeHex($dec)
    {
        if (!is_string($dec) && !ctype_digit($dec)) {
            throw new \Exception(sprintf('Argument is expected to be a string of decimal numbers. You passed in "%s"', gettype($dec)));
        }

        $hex = '';

        while (gmp_cmp($dec, 0) > 0) {
            list ($dec, $rem) = gmp_div_qr($dec, 16);
            $hex = substr(self::HEX_CHARS, gmp_intval($rem), 1) . $hex;
        }

        return $hex;
    }

    /**
     * Decodes a hexadecimal value into decimal.
     *
     * @param string $hex
     * @return string
     */
    public static function decodeHex($hex)
    {
        if (!is_string($hex) || !ctype_xdigit($hex) && '0x' != substr($hex, 0, 2)) {
            throw new \Exception('Argument must be a string of hex digits.');
        }

        $hex = strtolower($hex);

        // if it has a prefix of 0x this needs to be trimed
        if (substr($hex, 0, 2) == '0x') {
            $hex = substr($hex, 2);
        }

        for ($dec = '0', $i = 0; $i < strlen($hex); $i++) {
            $current = strpos(self::HEX_CHARS, $hex[$i]);
            $dec     = gmp_add(gmp_mul($dec, 16), $current);
        }

        return gmp_strval($dec);
    }
}
