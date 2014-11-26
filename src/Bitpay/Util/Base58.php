<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License 
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay\Util;

/**
 * Utility class for encoding/decoding BASE-58 data
 *
 * @package Bitcore
 */
final class Base58
{
    /**
     * @var string
     */
    const BASE58_CHARS = '123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz';

    /**
     * Encodes $data into BASE-58 format
     *
     * @param string $data
     *
     * @return string
     */
    public static function encode($data)
    {
        if (strlen($data) % 2 != 0 || strlen($data) == 0) {
            throw new \Exception('Invalid Length');
        }

        $code_string = self::BASE58_CHARS;
        $x = Util::decodeHex($data);
        $output_string = '';

        while (gmp_cmp($x, '0') > 0) {
            list($x, $r) = gmp_div_qr($x, 58);
            $output_string .= substr($code_string, gmp_intval($r), 1);
        }

        for ($i = 0; $i < strlen($data) && substr($data, $i, 2) == '00'; $i += 2) {
            $output_string .= substr($code_string, 0, 1);
        }

        $output_string = strrev($output_string);

        return $output_string;
    }

    /**
     * Decodes $data from BASE-58 format
     *
     * @param string $data
     *
     * @return string
     */
    public static function decode($data)
    {
        $lengthOfData = strlen($data);
        for ($return = '0', $i = 0; $i < $lengthOfData; $i++) {
            $current = strpos(self::BASE58_CHARS, $data[$i]);
            $return  = gmp_mul($return, '58');
            $return  = gmp_strval(gmp_add($return, $current));
        }

        $return = Util::encodeHex($return);

        for ($i = 0; $i < strlen($data) && substr($data, $i, 1) == '1'; $i++) {
            $return = '00' . $return;
        }

        if (strlen($return) % 2 != 0) {
            $return = '0' . $return;
        }

        return $return;
    }
}
