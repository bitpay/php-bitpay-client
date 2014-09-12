<?php

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
     * Give a class name and it will require the file
     *
     * @param string $class
     */
    public static function autoload($class)
    {
        if (0 !== strpos($class, 'Bitpay')) {
            return;
        }

        $file = __DIR__ . '/../' . str_replace(array('\\'), array('/'), $class) . '.php';
        if (is_file($file)) {
            require_once $file;
            return;
        }

        throw new \Exception(sprintf('Class "%s" Not Found', $class));
    }
}
