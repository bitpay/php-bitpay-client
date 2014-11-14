<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay\Math;

class MathTest extends \PHPUnit_Framework_TestCase
{
	public function testIsEngineSet()
	{
		$engine = new Math();
		$this->assertNotNull($engine);
	}

}