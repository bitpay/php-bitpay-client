<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay\Math;

function function_exists($func)
{
	return false;
}

class MathTest extends \PHPUnit_Framework_TestCase
{
	public function testIsEngineSet()
	{
		Math::setEngine(null);
		$this->assertNull(Math::getEngine());
		$engine = $this->getMock('Bitpay\Math\EngineInterface');
		Math::setEngine($engine);
		$this->assertInstanceOf('Bitpay\Math\EngineInterface', Math::getEngine());
	}

  /**
   * @requires  extension gmp
   * @runInSeparateProcess
   */
	public function testGmpMath()
  	{
	    if (!extension_loaded('gmp'))
	    {
	      $this->markTestSkipped('The GMP extension is NOT loaded! You must enable it to run this test');
	    }
	    Math::add("3324234234234234234", "3324234234234234234");
	    $this->assertEquals(new GmpEngine(), Math::getEngine());
	}

  /**
   * @requires  extension bcmath
   * @runInSeparateProcess
   */
	public function testBcMath()
	{
	    if (!extension_loaded('bcmath'))
	    {
	      $this->markTestSkipped('The Bcmath extension is NOT loaded! You must enable it to run this test');
	    } elseif (extension_loaded('gmp')) {
	      $this->markTestSkipped('The GMP extension is loaded! You must remove it to run this test');
	    }
	    Math::add("3324234234234234234", "3324234234234234234");
	    $this->assertEquals(new BcEngine(), Math::getEngine());
  	}
}
