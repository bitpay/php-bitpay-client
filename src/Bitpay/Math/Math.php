<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License 
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay\Math;

class Math
{
	private static $engine = null;

	public static function setEngine(EngineInterface $engine) {
		static::$engine = $engine;
	}
    /**
     * @param String $a Numeric String
     * @param String $b Numeric String
     */
    public static function __callStatic($name, $arguments)
    {
    	if (is_null(static::$engine)) {
			static::$engine = new GmpEngine();
		}
    	return call_user_func_array(array(static::$engine, $name), $arguments);
    }

}
