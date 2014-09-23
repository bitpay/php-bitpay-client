<?php
/**
 * The MIT License (MIT)
 *
 * Copyright (c) 2014 BitPay, Inc.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace Bitpay\Util;

/**
 * Utility class for generating an operating system & enivironment fingerprint
 */
class Fingerprint
{
    public static $sigData = null;
    public static $finHash = null;

    /**
     * Generates a string of environment information and
     * takes the hash of that value to use as the env
     * fingerprint.
     *
     * @param void
     * @return string
     */
    final public static function generate()
    {
        self::$finHash = '';
        self::$sigData = array();

        if (php_sapi_name() != 'cli') {
            self::$sigData[] = $_SERVER['SERVER_SOFTWARE'];
            self::$sigData[] = $_SERVER['SERVER_NAME'];
            self::$sigData[] = $_SERVER['SERVER_ADDR'];
            self::$sigData[] = $_SERVER['SERVER_PORT'];
            self::$sigData[] = $_SERVER['DOCUMENT_ROOT'];
        } else {
            self::$sigData[] = phpversion();
        }

        if (function_exists('get_current_user')) {
            self::$sigData[] = get_current_user();
        }

        if (function_exists('php_uname')) {
            self::$sigData[] = php_uname('s') . 
                php_uname('n') . 
                php_uname('m') . 
                PHP_OS . 
                PHP_SAPI . 
                ICONV_IMPL . 
                ICONV_VERSION;
        }

        if (function_exists('sha1_file')) {
            self::$sigData[] = sha1_file(__FILE__);
        } else {
            self::$sigData[] = md5_file(__FILE__);
        }

        foreach (self::$sigData as $key => $value) {
            self::$finHash .= trim($value);
        }

        if (function_exists('sha1')) {
            self::$finHash = sha1(str_ireplace(' ', '', self::$finHash) . 
                strlen(self::$finHash) . 
                metaphone(self::$finHash));

            return sha1(self::$finHash);
        } else {
            self::$finHash = md5(str_ireplace(' ', '', self::$finHash) . 
                strlen(self::$finHash) . 
                metaphone(self::$finHash));

            return md5(self::$finHash);
        }
    }

    /**
     *
     */
    public function __toString()
    {
        if (empty(self::$finHash)) {
            self::generate();
        }
    
        return self::$finHash;
    }
}
