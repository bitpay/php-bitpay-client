<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay;

/**
 * @package Bitpay
 */
class Autoloader
{
    /**
     * Register the autoloader, by default this will put the BitPay autoloader
     * first on the stack, to append the autoloader, pass `false` as an argument.
     *
     * Some applications will throw exceptions if the class isn't found and
     * some are not compatible with PSR standards.
     *
     * @param boolean $prepend
     */
    public static function register($prepend = true)
    {
        spl_autoload_register(array(__CLASS__, 'autoload'), true, (bool) $prepend);
    }

    /**
     * Unregister this autoloader
     */
    public static function unregister()
    {
        spl_autoload_unregister(array(__CLASS__, 'autoload'));
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

        if (0 === strpos($class, 'Bitpay\\')) {
            $isBitpay = true;
        }

        $file = __DIR__.'/../'.str_replace(array('\\'), array('/'), $class).'.php';

        if (is_file($file) && is_readable($file)) {
            require_once $file;

            return true;
        }

        /**
         * Only want to throw exceptions if class is under bitpay namespace
         */
        if ($isBitpay) {
            throw new \Exception(sprintf('Class "%s" Not Found', $class));
        }
    }
}
