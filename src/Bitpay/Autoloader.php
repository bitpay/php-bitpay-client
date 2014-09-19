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

namespace Bitpay;

/**
 *
 * @package Bitpay
 */
class Autoloader
{

    /**
     * Register the autoloader
     */
    public static function register()
    {
        spl_autoload_register(array(__CLASS__, 'autoload'));
    }

    /**
     * Give a class name and it will require the file.
     *
     * @param string $class
     * @return bool
     */
    public static function autoload($class)
    {
        $isBitpay = false;

        if (0 === strpos($class, 'Bitpay')) {
            $isBitpay = true;
        }

        $file = __DIR__ . '/../' . str_replace(array('\\'), array('/'), $class) . '.php';

        if (is_file($file) && is_readable($file)) {
            require_once $file;

            return true;
        } else {
            throw new \Exception(sprintf('Class file "%s" is not readable or has been moved.', $file));
        }

        if ($isBitpay) {
            throw new \Exception(sprintf('Class "%s" Not Found', $class));
        }
    }
}
