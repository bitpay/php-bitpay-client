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
    protected static $sigData;
    protected static $finHash;

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
        if (null !== self::$finHash) {
            return self::$finHash;
        }

        self::$finHash = '';
        self::$sigData = array();

        $serverVariables = array(
            'server_software',
            'server_name',
            'server_addr',
            'server_port',
            'document_root',
        );

        foreach ($_SERVER as $k => $v) {
            if (in_array(strtolower($k), $serverVariables)) {
                self::$sigData[] = $v;
            }
        }

        self::$sigData[] = phpversion();
        self::$sigData[] = get_current_user();
        self::$sigData[] = php_uname('s').php_uname('n').php_uname('m').PHP_OS.PHP_SAPI.ICONV_IMPL.ICONV_VERSION;
        self::$sigData[] = sha1_file(__FILE__);

        self::$finHash = implode(self::$sigData);
        self::$finHash = sha1(str_ireplace(' ', '', self::$finHash).strlen(self::$finHash).metaphone(self::$finHash));
        self::$finHash = sha1(self::$finHash);

        return self::$finHash;
    }
}
