<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License 
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
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
     * @param  string $class
     * @return bool
     */
    public static function autoload($class)
    {
        $isBitpay = false;

        if (0 === strpos($class, 'Bitpay')) {
            $isBitpay = true;
        }

        $file = __DIR__.'/../'.str_replace(array('\\'), array('/'), $class).'.php';

        if (is_file($file) && is_readable($file)) {
            require_once $file;

            return true;
        } else {
            //throw new \Exception(sprintf('Class file "%s" is not readable or has been moved.', $file));
        }

        if ($isBitpay) {
            throw new \Exception(sprintf('Class "%s" Not Found', $class));
        }
    }
}
