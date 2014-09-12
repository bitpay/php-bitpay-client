<?php

namespace Bitpay\Util;

/**
 * @package Bitcore
 */
class Util
{

    /**
     * @var string
     */
    const HEX_CHARS = '0123456789ABCDEF';

    /**
     * @param string $data
     *
     * @return string
     */
    public static function sha256($data, $binary = false)
    {
        return openssl_digest($data, 'sha256', $binary);
    }

    /**
     * @param string $data
     *
     * @return string
     */
    public static function sha512($data)
    {
        return openssl_digest($data, 'sha512');
    }

    /**
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
     * @param string $data
     *
     * @return string
     */
    public static function ripe160($data)
    {
        return openssl_digest($data, 'ripemd160');
    }

    /**
     * @param string $data
     *
     * @return string
     */
    public static function sha256ripe160($data)
    {
        return self::ripe160(self::sha256($data));
    }

    /**
     * @param string $data
     *
     * @return string
     */
    public static function twoSha256($data, $binary = false)
    {
        return self::sha256(self::sha256($data, $binary), $binary);
    }

    /**
     * @see http://en.wikipedia.org/wiki/Cryptographic_nonce
     *
     * @return string
     */
    public static function nonce()
    {
        return microtime(true);
    }

    /**
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
     * @param string $hex
     *
     * @return string
     */
    public static function encodeHex($dec)
    {
        if (!is_string($dec)) {
            throw new \Exception(sprintf('argument is expected to be a string. You passed in "%s"', gettype($dec)));
        }

        $hex = '';
        while (0 < gmp_cmp($dec, 0)) {
            list ($dec, $r) = gmp_div_qr($dec, 16);
            $hex .= substr(self::HEX_CHARS, gmp_intval($r), 1);
        }

        return $hex;
    }

    /**
     * @param string $hex
     *
     * @return string
     */
    public static function decodeHex($hex)
    {
        $hex = strtoupper($hex);
        for ($dec = 0, $i = 0; $i < strlen($hex); $i++) {
            $c   = strpos(self::HEX_CHARS, $hex[$i]);
            $dec = gmp_add(gmp_mul($dec, 16), $c);
        }

        return gmp_strval($dec);
    }
}
